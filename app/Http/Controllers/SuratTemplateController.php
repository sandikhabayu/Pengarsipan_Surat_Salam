<?php

namespace App\Http\Controllers;

use App\Models\SuratTemplate;
use App\Models\SuratKeluar;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use HTMLPurifier;
use HTMLPurifier_Config;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class SuratTemplateController extends Controller
{
    private function cleanHTML($html)
{
    // Decode HTML entities
    $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    
    // Hapus tag img
    $html = preg_replace('/<img[^>]*>/i', '', $html);
    $html = preg_replace('/<\/img>/i', '', $html);
    
    // Izinkan tag dan atribut styling
    $html = strip_tags($html, '<p><br><b><strong><i><em><u><strike><sub><sup><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6><ul><ol><li><div>'); // Tambahkan <div>
    
    // ===== PRESERVE STYLING ATTRIBUTES =====
    // 1. Keep alignment attributes
    $html = preg_replace_callback('/<(p|div|td|th|table)([^>]*)>/i', function($matches) {
        $tag = $matches[1];
        $attrs = $matches[2];
        
        $newAttrs = '';
        
        // Keep align attribute
        if (preg_match('/align=["\']([^"\']*)["\']/', $attrs, $alignMatch)) {
            $newAttrs .= ' align="' . htmlspecialchars($alignMatch[1]) . '"';
        }

        // Keep border attributes
        if (preg_match('/border=["\']([^"\']*)["\']/', $attrs, $borderMatch)) {
            $newAttrs .= ' border="' . htmlspecialchars($borderMatch[1]) . '"';
        }
        
        // Keep style attribute (filtered)
        if (preg_match('/style=["\']([^"\']*)["\']/', $attrs, $styleMatch)) {
            $style = $styleMatch[1];
            // Hanya izinkan properti CSS yang aman
            $allowedStyles = [
                'text-align' => true,
                'font-weight' => true,
                'font-style' => true,
                'text-decoration' => true,
                'border' => true,
                'border-top' => true,
                'border-right' => true,
                'border-bottom' => true,
                'border-left' => true,
                'border-width' => true,
                'border-style' => true,
                'border-color' => true,
                'border-collapse' => true,
                'width' => true,
                'padding' => true,
                'margin' => true,
                'vertical-align' => true
            ];
            
            $filteredStyles = [];
            $styleParts = explode(';', $style);
            foreach ($styleParts as $part) {
                $part = trim($part);
                if (empty($part)) continue;
                
                $parts = explode(':', $part, 2);
                if (count($parts) === 2) {
                    $property = trim($parts[0]);
                    $value = trim($parts[1]);
                    
                    if (isset($allowedStyles[$property])) {
                        $filteredStyles[] = $property . ':' . $value;
                    }
                }
            }
            
            if (!empty($filteredStyles)) {
                $newAttrs .= ' style="' . implode('; ', $filteredStyles) . '"';
            }
        }
        
        // Keep rowspan/colspan for table cells
        if ($tag === 'td' || $tag === 'th') {
            if (preg_match('/rowspan=["\']([^"\']*)["\']/', $attrs, $rowMatch)) {
                $newAttrs .= ' rowspan="' . htmlspecialchars($rowMatch[1]) . '"';
            }
            if (preg_match('/colspan=["\']([^"\']*)["\']/', $attrs, $colMatch)) {
                $newAttrs .= ' colspan="' . htmlspecialchars($colMatch[1]) . '"';
            }
        }
        
        // Keep width and border for tables
        if ($tag === 'table') {
            if (preg_match('/width=["\']([^"\']*)["\']/', $attrs, $widthMatch)) {
                $newAttrs .= ' width="' . htmlspecialchars($widthMatch[1]) . '"';
            }
            if (preg_match('/cellpadding=["\']([^"\']*)["\']/', $attrs, $cpMatch)) {
                $newAttrs .= ' cellpadding="' . htmlspecialchars($cpMatch[1]) . '"';
            }
            if (preg_match('/cellspacing=["\']([^"\']*)["\']/', $attrs, $csMatch)) {
                $newAttrs .= ' cellspacing="' . htmlspecialchars($csMatch[1]) . '"';
            }
        }
        
        return '<' . $tag . $newAttrs . '>';
    }, $html);
    
    // 2. Hapus atribut yang tidak perlu tapi biarkan class yang berguna
    $html = preg_replace('/\s+(class|lang|dir|id|name|bgcolor|valign)=["\'][^"\']*["\']/i', '', $html);
    
    // 3. Pastikan semua tabel memiliki border collapse - PERBAIKAN DI SINI
    $html = preg_replace_callback('/<table([^>]*)>/i', function($matches) {
        $attrs = $matches[1];
        
        if (!str_contains($attrs, 'style=')) {
            return '<table' . $attrs . ' style="border-collapse: collapse;">';
        } elseif (!str_contains($attrs, 'border-collapse')) {
            return preg_replace('/style=["\']([^"\']*)["\']/', 'style="$1; border-collapse: collapse;"', '<table' . $attrs . '>');
        }
        return '<table' . $attrs . '>';
    }, $html);
    
    // 4. Normalize line breaks
    $html = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br><br>', $html);
    // 5. Hapus div kosong
    $html = preg_replace('/<div>\s*<\/div>/i', '', $html);
    
    return trim($html);
}

    // Menampilkan daftar surat dengan pencarian dan filter
    public function index(Request $request)
    {
        $query = SuratTemplate::query();
        
        // Filter berdasarkan jenis surat
        if ($request->has('jenis_surat') && $request->jenis_surat != '') {
            $query->where('jenis_surat', $request->jenis_surat);
        }
        
        // Pencarian berdasarkan perihal/nomor surat
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('perihal', 'like', "%{$search}%")
                  ->orWhere('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('kepada', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan tanggal mulai
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        
        // Filter berdasarkan tanggal selesai
        if ($request->has('tanggal_selesai') && $request->tanggal_selesai != '') {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }
        
        // Filter berdasarkan bulan
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal', $request->bulan);
        }
        
        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal', $request->tahun);
        }
        
        // Urutkan berdasarkan tanggal terbaru
        $surats = $query->latest()->get();
        
        // Data untuk dropdown filter
        $tahunList = SuratTemplate::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
            
        $bulanList = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        // Jenis surat untuk filter
        $jenisSuratList = [
            'kepala_desa' => 'Surat Kepala Desa',
            'sekretariat' => 'Surat Sekretariat'
        ];
        
        return view('petugas.surat-template.index', compact('surats', 'tahunList', 'bulanList', 'jenisSuratList'));
    }

    // Menampilkan form create
    public function create()
    {
        $nomorUrut = NomorSuratService::generateNomorUrut();
        $jenisSuratList = [
            'kepala_desa' => 'Surat Kepala Desa',
            'sekretariat' => 'Surat Sekretariat'
        ];
        
        return view('petugas.surat-template.create', compact('nomorUrut', 'jenisSuratList'));
    }

    // Menyimpan surat baru
    public function store(Request $request)
    {
        // Debug: Cek apa yang diterima
    \Log::info('ISI SURAT RAW:', ['html' => $request->isi_surat]);
    
    // Debug: Cek apakah ada tag img
    if (strpos($request->isi_surat, '<img') !== false) {
        \Log::warning('Ditemukan tag img dalam input!');
        \Log::warning('HTML mengandung img:', ['found' => true]);
        
        // Hapus img sekarang juga
        $cleanInput = preg_replace('/<img[^>]*>/i', '', $request->isi_surat);
        \Log::info('Setelah hapus img:', ['html' => $cleanInput]);
        
        // Ganti request data
        $request->merge(['isi_surat' => $cleanInput]);
    }
    
        try {
            \Log::info('== MULAI PROSES STORE SURAT TEMPLATE ===');
            \Log::info('Request data (tanpa isi_surat):', 
            array_diff_key($request->all(), ['isi_surat' => '']));
        \Log::info('Panjang isi_surat:', ['length' => strlen($request->isi_surat)]);
        
        // Cek apakah ada tabel tanpa border
        if (strpos($request->isi_surat, 'border="0"') !== false || 
            strpos($request->isi_surat, 'border="none"') !== false) {
            \Log::info('Ditemukan tabel tanpa border');
        }

            // Validasi input
            $validated = $request->validate([
                'jenis_surat' => 'required|in:kepala_desa,sekretariat',
                'nomor_urut' => 'required|string',
                'format_surat' => 'required|string|max:255',
                'lampiran' => 'required|string|max:255',
                'tanggal' => 'required|date', 
                'kepada' => 'required|string', 
                'perihal' => 'required|string',
                'isi_surat' => 'required',
            ]);

            // Bersihkan HTML sebelum disimpan
            $cleanedHTML = $this->cleanHTML($validated['isi_surat']);
             // Log cleaned HTML untuk debugging
            \Log::info('HTML setelah dibersihkan (500 karakter pertama):', 
                ['html' => substr($cleanedHTML, 0, 500)]
            );
            // Cek tabel setelah cleaning
            if (preg_match_all('/<table[^>]*>/i', $cleanedHTML, $tableMatches)) {
                \Log::info('Jumlah tabel ditemukan:', ['count' => count($tableMatches[0])]);
                foreach ($tableMatches[0] as $index => $tableTag) {
                    \Log::info('Tabel ' . ($index+1) . ':', ['tag' => $tableTag]);
                }
            }

            // === PERBAIKAN UTAMA: Hapus img sebelum cleaning ===
            $html = $validated['isi_surat'];
            $html = preg_replace('/<img[^>]*>/i', '', $html);
            $html = preg_replace('/<\/img>/i', '', $html);
            
            try {
                $cleanedHTML = $this->cleanHTML($html);
            } catch (\Exception $e) {
                \Log::warning('HTMLPurifier error, menggunakan fallback: ' . $e->getMessage());
                // Fallback: strip tags sederhana
                $cleanedHTML = strip_tags($html, 
                    '<p><br><b><strong><i><em><u><strike><sub><sup><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6><ul><ol><li>'
                );
                // Hapus semua atribut
                $cleanedHTML = preg_replace('/<(\w+)[^>]*>/', '<$1>', $cleanedHTML);
            }

            // Cek apakah hasil kosong
            if (empty(trim($cleanedHTML))) {
                \Log::warning('Cleaned HTML kosong, gunakan original dengan strip tags');
                $cleanedHTML = strip_tags($html, '<p><br><b><strong><i><em><u><strike><sub><sup><table><tr><td><th><thead><tbody><h1><h2><h3><h4><h5><h6><ul><ol><li>');
                // Hapus atribut
                $cleanedHTML = preg_replace('/<(\w+)[^>]*>/', '<$1>', $cleanedHTML);
            }

            // Validasi nomor urut tidak duplikat
            if (NomorSuratService::isNomorUrutExists($validated['nomor_urut'])) {
                return back()->withInput()->withErrors([
                    'nomor_urut' => 'Nomor urut ini sudah digunakan. Silakan gunakan nomor lain.'
                ]);
            }

            // Validasi nomor urut tidak duplikat
            if (NomorSuratService::isNomorUrutExists($validated['nomor_urut'])) {
                return back()->withInput()->withErrors([
                    'nomor_urut' => 'Nomor urut ini sudah digunakan. Silakan gunakan nomor lain.'
                ]);
            }

            // Generate nomor surat berdasarkan jenis
            if ($validated['jenis_surat'] == 'kepala_desa') {
                // Format: 001/SKD/VI/2024 (SKD = Surat Kepala Desa)
                $nomorSurat = $validated['nomor_urut'] . '/SKD/' . Carbon::parse($validated['tanggal'])->format('m') . '/' . Carbon::parse($validated['tanggal'])->format('Y');
            } else {
                // Format: 001/SS/VI/2024 (SS = Surat Sekretariat)
                $nomorSurat = $validated['nomor_urut'] . '/SS/' . Carbon::parse($validated['tanggal'])->format('m') . '/' . Carbon::parse($validated['tanggal'])->format('Y');
            }
            
            \Log::info('Nomor surat: ' . $nomorSurat);

            // Simpan ke tabel surat_templates
            $suratTemplate = SuratTemplate::create([
                'jenis_surat' => $validated['jenis_surat'],
                'nomor_surat' => $nomorSurat,
                'format_surat' => $validated['format_surat'],
                'lampiran' => $validated['lampiran'],
                'tanggal' => $validated['tanggal'], 
                'kepada' => $validated['kepada'], 
                'perihal' => $validated['perihal'],
                'isi_surat' => $cleanedHTML,
            ]);
            \Log::info('Data di database:', [
                'isi_surat' => $suratTemplate->isi_surat
            ]);
            \Log::info('SuratTemplate berhasil disimpan: ID ' . $suratTemplate->id);

            // Generate PDF isi surat
            $filename = 'surat-' . $suratTemplate->jenis_surat . '-' . $suratTemplate->id . '-' . time() . '.pdf';
            $filePath = 'surat-keluar/' . $filename;

            Carbon::setLocale('id');
            $pdf = Pdf::loadView('petugas.surat-template.pdf', [
                'surats' => $suratTemplate,
                'jenis_surat' => $validated['jenis_surat']
            ])->setPaper('a4', 'portrait');

            Storage::disk('public')->put($filePath, $pdf->output());
            \Log::info('PDF berhasil disimpan: ' . $filePath);

            // Simpan juga ke SuratKeluar
            $kodeSurat = $validated['jenis_surat'] == 'kepala_desa' ? 'SKD-' : 'SS-';
            
            SuratKeluar::create([
                'kode_surat' => $kodeSurat . $validated['nomor_urut'],
                'nomor_surat' => $nomorSurat,
                'lampiran' => $validated['lampiran'],
                'tanggal_keluar' => $validated['tanggal'],
                'tujuan' => $validated['kepada'],
                'perihal' => $validated['perihal'],
                'jenis_surat' => $validated['jenis_surat'],
                'file_path' => $filePath,
            ]);

            // Redirect ke halaman index dengan success message
            return redirect()
                ->route('petugas.surat-template.index')
                ->with('success', 'Surat ' . ($validated['jenis_surat'] == 'kepala_desa' ? 'Kepala Desa' : 'Sekretariat') . ' berhasil dibuat.');

        } catch (\Throwable $e) {
            \Log::error('ERROR saat membuat surat: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method lainnya tetap sama...
    public function show($id)
    {
        $suratTemplate = SuratTemplate::findOrFail($id);
        $jenisSuratLabels = [
            'kepala_desa' => 'Surat Kepala Desa',
            'sekretariat' => 'Surat Sekretariat'
        ];
        
        return view('petugas.surat-template.show', compact('suratTemplate', 'jenisSuratLabels'));
    }

    public function edit($id)
    {
        $suratTemplate = SuratTemplate::findOrFail($id);
        
        // Debug data
    \Log::info('Isi surat dari database:', [
        'raw' => $suratTemplate->isi_surat,
        'length' => strlen($suratTemplate->isi_surat),
        'first_100_chars' => substr($suratTemplate->isi_surat, 0, 100)
    ]);
        // Extract nomor urut dari nomor_surat untuk form edit
        $nomorParts = explode('/', $suratTemplate->nomor_surat);
        $nomorUrut = $nomorParts[0] ?? '';
        
        // DECODE HTML ENTITIES sebelum dikirim ke view
        $suratTemplate->isi_surat = html_entity_decode($suratTemplate->isi_surat, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Bersihkan backslashes jika ada
        $suratTemplate->isi_surat = stripslashes($suratTemplate->isi_surat);
        
         // Hapus encoding ganda jika ada
    $suratTemplate->isi_surat = str_replace(
        ['&lt;', '&gt;', '&amp;', '&quot;', '&#039;'],
        ['<', '>', '&', '"', "'"],
        $suratTemplate->isi_surat
    );

        $jenisSuratList = [
            'kepala_desa' => 'Surat Kepala Desa',
            'sekretariat' => 'Surat Sekretariat'
        ];

        // Debug: Cek data yang dikirim ke view
        \Log::info('Data surat template untuk edit:', [
            'id' => $suratTemplate->id,
            'isi_surat_length' => strlen($suratTemplate->isi_surat),
            'isi_surat_preview' => substr($suratTemplate->isi_surat, 0, 100)
        ]);
        
        return view('petugas.surat-template.edit', compact('suratTemplate', 'nomorUrut', 'jenisSuratList'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_surat' => 'required|in:kepala_desa,sekretariat',
            'nomor_urut' => 'required|string',
            'lampiran' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tanggal' => 'required|date',
            'kepada' => 'required|string',
            'isi_surat' => 'required',
        ]);

        $suratTemplate = SuratTemplate::findOrFail($id);
        $oldNomorSurat = $suratTemplate->nomor_surat;

        // Simpan dengan htmlspecialchars_decode jika perlu
        $suratTemplate = SuratTemplate::find($id);
        $suratTemplate->isi_surat = htmlspecialchars_decode($request->isi_surat);
        $suratTemplate->save();
        
        // Bersihkan HTML sebelum update
        $html = $validated['isi_surat'];
        $html = preg_replace('/<img[^>]*>/i', '', $html);
        $html = preg_replace('/<\/img>/i', '', $html);
        $cleanedHTML = $this->cleanHTML($html);
        
        // Generate nomor surat baru berdasarkan jenis
        if ($validated['jenis_surat'] == 'kepala_desa') {
            $nomorSurat = $validated['nomor_urut'] . '/SKD/' . Carbon::parse($validated['tanggal'])->format('m') . '/' . Carbon::parse($validated['tanggal'])->format('Y');
        } else {
            $nomorSurat = $validated['nomor_urut'] . '/SS/' . Carbon::parse($validated['tanggal'])->format('m') . '/' . Carbon::parse($validated['tanggal'])->format('Y');
        }

        // Validasi nomor surat tidak duplikat (kecuali untuk record ini)
        $existingNomor = SuratTemplate::where('nomor_surat', $nomorSurat)
            ->where('id', '!=', $suratTemplate->id)
            ->exists();

        if ($existingNomor) {
            return back()->withInput()->withErrors([
                'nomor_urut' => 'Nomor surat ini sudah digunakan. Silakan gunakan nomor lain.'
            ]);
        }

        // Update SuratTemplate
        $suratTemplate->update([
            'jenis_surat' => $validated['jenis_surat'],
            'nomor_surat' => $nomorSurat,
            'lampiran' => $validated['lampiran'],
            'perihal' => $validated['perihal'],
            'tanggal' => $validated['tanggal'],
            'kepada' => $validated['kepada'],
            'isi_surat' => $cleanedHTML,
        ]);

        // Update juga di SuratKeluar jika ada
        $suratKeluar = SuratKeluar::where('nomor_surat', $oldNomorSurat)->first();
        if ($suratKeluar) {
            $kodeSurat = $validated['jenis_surat'] == 'kepala_desa' ? 'SKD-' : 'SS-';
            
            $suratKeluar->update([
                'kode_surat' => $kodeSurat . $validated['nomor_urut'],
                'nomor_surat' => $nomorSurat,
                'lampiran' => $validated['lampiran'],
                'tanggal_keluar' => $validated['tanggal'],
                'tujuan' => $validated['kepada'],
                'perihal' => $validated['perihal'],
                'jenis_surat' => $validated['jenis_surat'],
            ]);
            
            // Regenerate PDF
            $filename = 'surat-' . $validated['jenis_surat'] . '-' . $suratTemplate->id . '-' . time() . '.pdf';
            $filePath = 'surat-keluar/' . $filename;

            $pdf = Pdf::loadView('petugas.surat-template.pdf', [
                'surats' => $suratTemplate,
                'jenis_surat' => $validated['jenis_surat']
            ])->setPaper('a4', 'portrait');

            Storage::disk('public')->put($filePath, $pdf->output());
            
            $suratKeluar->update(['file_path' => $filePath]);
        }

        return redirect()->route('petugas.surat-template.show', $id)
            ->with('success', 'Surat berhasil diperbarui');
    }

    public function destroy($id)
    {
        try {
            $suratTemplate = SuratTemplate::findOrFail($id);
            $nomorSurat = $suratTemplate->nomor_surat;
            
            Log::info("Memulai penghapusan SuratTemplate ID: {$id}");
            
            // Hapus juga dari SuratKeluar jika ada
            $suratKeluar = SuratKeluar::where('nomor_surat', $nomorSurat)->first();
            if ($suratKeluar) {
                // Hapus file PDF dari storage
                if ($suratKeluar->file_path && Storage::disk('public')->exists($suratKeluar->file_path)) {
                    Storage::disk('public')->delete($suratKeluar->file_path);
                    Log::info("File PDF dihapus: {$suratKeluar->file_path}");
                }
                $suratKeluar->delete();
                Log::info("SuratKeluar dengan nomor {$nomorSurat} juga dihapus");
            }
            
            // Hapus SuratTemplate
            $suratTemplate->delete();
            
            Log::info("Penghapusan SuratTemplate ID: {$id} selesai");

            return redirect()->route('petugas.surat-template.index')
                ->with('success', 'Surat berhasil dihapus dari kedua tabel.');

        } catch (\Exception $e) {
            Log::error("Gagal menghapus SuratTemplate: " . $e->getMessage());
            return redirect()->route('petugas.surat-template.index')
                ->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }

    public function download($id)
{
    $surats = SuratTemplate::findOrFail($id);
    $filename = 'surat-' . ($surats->jenis_surat == 'kepala_desa' ? 'kepala-desa' : 'sekretariat') . '-' . $surats->id . '.pdf';

    // Format HTML untuk mPDF
    $html = $this->formatHTMLForMPDF($surats);
    
    // Dapatkan default config font
    $defaultConfig = (new ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    
    $defaultFontConfig = (new FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    // Konfigurasi mPDF yang lebih lengkap
    $mpdfConfig = [
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 20,
        'margin_bottom' => 20,
        'margin_header' => 0,
        'margin_footer' => 0,
        'tempDir' => storage_path('app/mpdf'),
        
        // Font configuration
        'default_font' => 'times', // Gunakan DejaVu untuk dukungan Unicode lebih baik
        // Untuk support gambar
        'showImageErrors' => false, // Nonaktifkan error gambar

        // Font configuration
        'fontDir' => array_merge($fontDirs, [
            storage_path('fonts'), // Tambahkan folder fonts custom
            base_path('vendor/mpdf/mpdf/data/fonts'), // Font default mPDF
        ]),

         'fontdata' => $fontData + [
            'tahoma' => [ // Gunakan font yang sudah ada
                'R' => 'tahoma.ttf',
                'B' => 'tahomabd.ttf',
            ],
            'arial' => [ // Atau gunakan Arial
                'R' => 'arial.ttf',
                'B' => 'arialbd.ttf',
                'I' => 'ariali.ttf',
                'BI' => 'arialbi.ttf',
            ],
            'dejavusans' => [ // Font default mPDF - PASTI ADA
                'R' => 'DejaVuSans.ttf',
                'B' => 'DejaVuSans-Bold.ttf',
                'I' => 'DejaVuSans-Oblique.ttf',
                'BI' => 'DejaVuSans-BoldOblique.ttf',
            ],
            'dejavusanscondensed' => [
                'R' => 'DejaVuSansCondensed.ttf',
                'B' => 'DejaVuSansCondensed-Bold.ttf',
                'I' => 'DejaVuSansCondensed-Oblique.ttf',
                'BI' => 'DejaVuSansCondensed-BoldOblique.ttf',
            ],
        ],
        
        'default_font' => 'dejavusans', // Font yang pasti ada
        
        // Untuk support gambar
        'showImageErrors' => false,
        
        // CSS support
        'CSSselectMedia' => 'mpdf', // Untuk mendukung media queries
        'useSubstitutions' => false, //pake times new roman
        'simpleTables' => false, // Nonaktifkan simpleTables untuk CSS yang lebih baik
        'packTableData' => true,
        'use_kwt' => true, // Keep-with-table
        'autoPageBreak' => true,
        'allow_output_buffering' => true,
        
        // Untuk support gambar base64
        'showImageErrors' => true,
        'img_dpi' => 96,
    ];
    try {
    $mpdf = new Mpdf($mpdfConfig);
    
    // Write HTML content
    $mpdf->WriteHTML($html);

    // Clean up temp images jika ada
    $this->cleanTempImages();
    
    // Output
    return response($mpdf->Output($filename, 'D'), 200)
        ->header('Content-Type', 'application/pdf');
    } catch (\Exception $e) {
        Log::error('Error generating PDF: ' . $e->getMessage());
        return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
    }
}

// public function download($id)
// {
//     $surats = SuratTemplate::findOrFail($id);
//     $filename = 'surat-' . ($surats->jenis_surat == 'kepala_desa' ? 'kepala-desa' : 'sekretariat') . '-' . $surats->id . '.pdf';

//     // Gunakan DomPDF yang sudah terinstall
//     $html = $this->formatHTMLForDomPDF($surats);
    
//     $pdf = Pdf::loadHTML($html)
//         ->setPaper('a4', 'portrait')
//         ->setOption('defaultFont', 'Times New Roman')
//         ->setOption('isRemoteEnabled', true); // Untuk enable gambar
    
//     return $pdf->download($filename);
// }

    // Fungsi untuk membersihkan gambar temporary
    private function cleanTempImages()
    {
        $tempDir = storage_path('app/public/temp_images');
        if (file_exists($tempDir)) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < strtotime('-1 hour')) {
                    unlink($file);
                }
            }
        }
    }

private function formatHTMLForMPDF($surats)
{
    $isi_surat = $surats->isi_surat;
    
    // Escape content untuk mPDF
    $isi_surat = html_entity_decode($isi_surat, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Process tables for better compatibility
    $isi_surat = $this->processTablesForMPDF($isi_surat);

    // PERBAIKAN UTAMA: Gunakan use() untuk mengakses variabel dalam callback
    $isi_surat = preg_replace_callback('/<table([^>]*)>/i', function($matches) use (&$isi_surat) {
        $attrs = $matches[1];
        $newAttrs = '';
        
        // Preserve existing border attribute
        if (preg_match('/border=["\']([^"\']*)["\']/', $attrs, $borderMatch)) {
            $borderValue = $borderMatch[1];
            $newAttrs .= ' border="' . $borderValue . '"';
            
            // Jika border="0" atau "none", atur style yang sesuai
            if ($borderValue === '0' || $borderValue === 'none') {
                $newAttrs .= ' style="border-collapse: collapse; border: none;"';
                // Hapus border dari cell juga
                $isi_surat = preg_replace('/<(td|th)([^>]*)>/i', 
                    '<$1$2 style="border: none; padding: 5px;">', 
                    $isi_surat);
            }
        } else {
            // Default border jika tidak ada
            $newAttrs .= ' border="1"';
        }
        
        // Preserve width
        if (preg_match('/width=["\']([^"\']*)["\']/', $attrs, $widthMatch)) {
            $newAttrs .= ' width="' . $widthMatch[1] . '"';
        } else {
            $newAttrs .= ' width="100%"';
        }
        
        // Preserve cellpadding dan cellspacing
        if (!str_contains($attrs, 'cellpadding=')) {
            $newAttrs .= ' cellpadding="5"';
        }
        if (!str_contains($attrs, 'cellspacing=')) {
            $newAttrs .= ' cellspacing="0"';
        }
        
        // Preserve style
        if (preg_match('/style=["\']([^"\']*)["\']/', $attrs, $styleMatch)) {
            $style = $styleMatch[1];
            // Pastikan ada border-collapse
            if (!str_contains($style, 'border-collapse')) {
                $newAttrs .= ' style="' . $style . '; border-collapse: collapse;"';
            } else {
                $newAttrs .= ' style="' . $style . '"';
            }
        } else {
            $newAttrs .= ' style="border-collapse: collapse;"';
        }
        
        return '<table' . $newAttrs . '>';
    }, $isi_surat);
    
    // Pastikan td/th memiliki style yang sesuai
    $isi_surat = preg_replace_callback('/<(td|th)([^>]*)>/i', function($matches) {
        $tag = $matches[1];
        $attrs = $matches[2];
        
        $newAttrs = $attrs;
        
        // Cek border pada tabel parent
        $hasBorder = true;
        if (str_contains($attrs, 'style=')) {
            // Jika cell sudah memiliki style border: none, pertahankan
            if (str_contains($attrs, 'border: none') || 
                str_contains($attrs, 'border:none')) {
                $hasBorder = false;
            }
        }
        
        // Tambahkan padding jika belum ada
        if (!str_contains($attrs, 'padding:')) {
            if ($newAttrs && !empty(trim($newAttrs))) {
                if (str_contains($newAttrs, 'style=')) {
                    $newAttrs = preg_replace('/style=["\']([^"\']*)["\']/', 
                        'style="$1; padding: 5px;"', $newAttrs);
                } else {
                    $newAttrs .= ' style="padding: 5px;"';
                }
            } else {
                $newAttrs = ' style="padding: 5px;"';
            }
        }
        
        return '<' . $tag . $newAttrs . '>';
    }, $isi_surat);

    // ===== PERBAIKAN UTAMA: PROSES GAMBAR =====
    // Cek apakah ada tag img dengan src base64
    if (preg_match_all('/<img[^>]+src="data:image\/([^;]+);base64,([^"]+)"[^>]*>/i', $isi_surat, $matches)) {
        foreach ($matches[0] as $key => $imgTag) {
            $imageType = strtolower($matches[1][$key]);
            $base64Data = $matches[2][$key];
            
            // Validasi tipe gambar
            $allowedTypes = ['jpeg', 'jpg', 'png', 'gif'];
            if (in_array($imageType, $allowedTypes)) {
                // Decode base64
                $imageData = base64_decode($base64Data);
                
                // Generate unique filename
                $filename = 'img_' . md5($base64Data) . '.' . $imageType;
                $imagePath = storage_path('app/public/temp_images/' . $filename);
                
                // Buat directory jika belum ada
                if (!file_exists(storage_path('app/public/temp_images'))) {
                    mkdir(storage_path('app/public/temp_images'), 0777, true);
                }
                
                // Save image to temp file
                file_put_contents($imagePath, $imageData);
                
                // Replace base64 dengan path ke file
                $absolutePath = $imagePath;
                $isi_surat = str_replace($imgTag, '<img src="file://' . $absolutePath . '" style="max-width: 100%; height: auto;">', $isi_surat);
            } else {
                // Jika tipe tidak didukung, hapus tag img
                $isi_surat = str_replace($imgTag, '', $isi_surat);
            }
        }
    }
    
    // Proses gambar dari src biasa (URL)
    $isi_surat = preg_replace_callback('/<img[^>]+src="([^"]+)"[^>]*>/i', function($matches) {
        $src = $matches[1];
        
        // Jika gambar dari public path
        if (strpos($src, '/') === 0) {
            $absolutePath = public_path($src);
            if (file_exists($absolutePath)) {
                return '<img src="file://' . $absolutePath . '" style="max-width: 100%; height: auto;">';
            }
        }
    
        // Untuk URL external atau gambar yang tidak ditemukan
        return '';
    }, $isi_surat);
    
    // Process tables for better compatibility
    $isi_surat = $this->processTablesForMPDF($isi_surat);
    
    // Path untuk logo kop surat
    $logoPath = public_path('images/logo_salam.png');
    $logoData = '';
    
    // Cek jika logo ada dan konversi ke base64 untuk inline
    if (file_exists($logoPath)) {
        // Untuk mPDF, gunakan file:// protocol
        $logoHtml = '<img src="file://' . str_replace('\\', '/', $logoPath) . '" class="logo-kop" alt="Logo Desa Salam">';
    }
    // HTML lengkap untuk mPDF
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            /* RESET DAN BASE STYLES */
            * { 
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body { 
                font-family: "Times New Roman", Times, serif;
                font-size: 12pt;
                line-height: 1.5;
                margin: 0;
                padding: 0;
                color: #000000;
            }
            
            /* KOP SURAT - Layout dengan gambar */
            .kop-surat-container {
                text-align: center;
                padding-bottom: 4px;
                border-bottom: 4px solid #000;
                position: relative;
            }
            .kop-surat-container p {
                margin: -4;
                padding: 0;
                font-size: 10pt;
            }
            .kop-surat {
                width: 100%;
            }
            
            .logo-kop {
                position: absolute;
                left: 20px;
                top: 10px;
                width: 100px;
                height: auto;
            }

            .alamat-kop {
                text-align: center;
            }
            
            .header-kop {
                text-align: center;
            }
            
            .header-kop h3 {
                margin: 2px 0;
                font-size: 14pt;
                font-weight: bold;
            }
            
            .header-kop p {
                margin: 2px 0;
                font-size: 11pt;
            }
            
            /* TABLE STYLES */
            table {
                border-collapse: collapse !important;
                width: 100%;
                margin: 10px 0;
                page-break-inside: avoid;
            }
            
            td, th {
                padding: 5px 8px !important;
                vertical-align: top;
                font-size: 11pt;
                border: 1px solid #000;
            }
            
            table.no-border,
            table.no-border td,
            table.no-border th {
                border: none !important;
            }
            
            /* LAYOUT STYLES */
            .two-column {
                width: 100%;
                overflow: hidden;
                margin-top: -20px;
            }
            
            .column-left {
                line-height: 1;
                float: left;
                width: 70%;
            }
            
            .column-right {
                line-height: 1;
                float: right;
                width: 30%;
                text-align: left;
            }
            
            /* SIGNATURE SECTION */
            .signature-container {
                width: 100%;
                margin-top: 10px;
                overflow: hidden;
            }
            
            .signature-left {
                float: left;
                width: 40%;
            }
            
            .signature-right {
                float: right;
                width: 40%;
                text-align: center;
            }
            
            /* TEXT STYLES */
            .text-justify {
                text-align: justify;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .text-left {
                text-align: left;
            }
            
            .mb-10 {
                margin-bottom: 10px;
            }
            
            .mb-20 {
                margin-bottom: 20px;
            }
            
            .mt-20 {
                margin-top: 20px;
            }
            
            .mt-50 {
                margin-top: 50px;
            }
            
            /* CLEARFIX */
            .clearfix::after {
                content: "";
                clear: both;
                display: table;
            }
            
            /* PAGE BREAK */
            .page-break {
                page-break-before: always;
            }
            
            /* ISI SURAT CONTENT */
            .isi-surat-content {
                padding: 0 20px;
                line-height: 1.6;
                text-align: justify;
            }
            
            .isi-surat-content p {
                margin: 8px 0;
            }
            
            .isi-surat-content img {
                max-width: 100% !important;
                height: auto !important;
                margin: 10px 0;
            }
            
            /* UTILITY */
            .bold {
                font-weight: bold;
            }
            
            .underline {
                text-decoration: underline;
            }
            
            .italic {
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <!-- KOP SURAT dengan gambar otomatis dari public/images/ -->
        <div class="kop-surat-container">
            <div class="kop-surat">
                <div style="width: 15%; float: left;">' . $logoHtml . '</div>
                <div class="header-kop" style="width: 70%;">
                    <h3>PEMERINTAH KABUPATEN PURWOREJO</h3>
                    <h3>KECAMATAN GEBANG</h3>
                    <h3>' . ($surats->jenis_surat == 'kepala_desa' ? 'KEPALA DESA' : 'SEKRETARIAT') . ' DESA SALAM</h3>
                </div>
                    <div style="width: 15%; float: right;"></div>
            </div>
            <p class="alamat-kop">Alamat: Desa Salam Kecamatan Gebang Kabupaten Purworejo Kode Pos 54191</p>
        </div>
        
        <!-- TANGGAL SURAT -->
        <div class="text-right">
            <p>Salam, ' . \Carbon\Carbon::parse($surats->tanggal)->locale('id')->isoFormat('D MMMM Y') . '</p>
        </div>
        
        <!-- INFORMASI SURAT -->
        <div class="two-column">
            <div class="column-left">
                <p><strong>Nomor</strong>    : ' . htmlspecialchars($surats->nomor_surat) . '</p>
                <p><strong>Lampiran</strong> : ' . htmlspecialchars($surats->lampiran) . '</p>
                <p><strong>Perihal</strong>  : ' . htmlspecialchars($surats->perihal) . '</p>
            </div>
            <div class="column-right">
                <p><strong>Kepada:</strong></p>
                <p>Yth. ' . htmlspecialchars($surats->kepada) . '</p>
                <p>Di</p>
                <p>     Gebang</p>
            </div>
        </div>
        
        <!-- ISI SURAT -->
        <div class="isi-surat-content">
            ' . $isi_surat . '
        </div>
        
        <!-- PENANDATANGAN -->
        <div class="signature-container">
            <div class="signature-left">
                <!-- Kosong -->
            </div>
            <div class="signature-right">
                <p>' . ($surats->jenis_surat == 'kepala_desa' ? 'Pj. Kepala Desa Salam' : 'Sekretaris Desa Salam') . '</p>
                <div style="height: 80px;"></div>
                <p class="bold">' . ($surats->jenis_surat == 'kepala_desa' ? 'BAMBANG LISTIONO AGUS,P.S.Sos' : 'MAULANA AMIRUL AKHMAD') . '</p>';
    
    if ($surats->jenis_surat == 'kepala_desa') {
        $html .= '
                <p>Pembina /IVa</p>
                <p>NIP.196808111989031008</p>';
    }
    
    $html .= '
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

private function processTablesForMPDF($html)
{
    // Pastikan semua tabel memiliki struktur yang kompatibel
    $html = preg_replace_callback('/<table([^>]*)>/i', function($matches) {
        $attrs = $matches[1];
        $newAttrs = '';
        
        // Cek border
        if (!preg_match('/border=/i', $attrs)) {
            $newAttrs .= ' border="1"';
        } else {
            // Pertahankan border yang ada
            preg_match('/border=["\']([^"\']*)["\']/i', $attrs, $borderMatch);
            if ($borderMatch && ($borderMatch[1] === '0' || $borderMatch[1] === 'none')) {
                $newAttrs .= ' class="no-border"';
            }
        }
        
        // Cek cellpadding
        if (!preg_match('/cellpadding=/i', $attrs)) {
            $newAttrs .= ' cellpadding="5"';
        }
        
        // Cek cellspacing
        if (!preg_match('/cellspacing=/i', $attrs)) {
            $newAttrs .= ' cellspacing="0"';
        }
        
        // Cek width
        if (!preg_match('/width=/i', $attrs)) {
            $newAttrs .= ' width="100%"';
        }
        
        // Cek style
        if (!preg_match('/style=/i', $attrs)) {
            $newAttrs .= ' style="border-collapse: collapse;"';
        } else {
            // Tambahkan border-collapse jika belum ada
            if (!str_contains($attrs, 'border-collapse')) {
                $attrs = preg_replace('/style=["\']([^"\']*)["\']/', 'style="$1; border-collapse: collapse;"', $attrs);
            }
        }
        
        return '<table' . $attrs . $newAttrs . '>';
    }, $html);
    
    // Tambahkan class untuk tabel tanpa border
    $html = preg_replace('/border=["\']0["\']/', 'border="0" class="no-border"', $html);
    $html = preg_replace('/border=["\']none["\']/', 'border="none" class="no-border"', $html);
    
    return $html;
}
    
    // Method untuk mendapatkan statistik surat
    public function getStats()
    {
        $stats = [
            'total' => SuratTemplate::count(),
            'kepala_desa' => SuratTemplate::where('jenis_surat', 'kepala_desa')->count(),
            'sekretariat' => SuratTemplate::where('jenis_surat', 'sekretariat')->count(),
        ];
        
        return $stats;
    }
}