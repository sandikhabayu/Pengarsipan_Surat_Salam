<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\NomorSuratService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratKeluar::query();

        if ($request->filled('search')) {
            $query->where('nomor_surat', 'like', '%' . $request->search . '%')
                  ->orWhere('tujuan', 'like', '%' . $request->search . '%')
                  ->orWhere('perihal', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_keluar', $request->tanggal);
        }

        $suratKeluars = $query->latest()->paginate(10);

        return view('petugas.surat-keluar.index', compact('suratKeluars'));
    }

    public function create()
    {
        // Generate nomor urut otomatis
        $nomorUrut = NomorSuratService::generateNomorUrut();
        $lastNomorUrut = NomorSuratService::getLastNomorUrut();
        
        // Generate kode surat otomatis
        $lastSurat = SuratKeluar::latest()->first();
        $lastNumber = $lastSurat ? (int) substr($lastSurat->kode_surat, -2) : 0;
        $kodeSurat = 'SK-'. str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

        return view('petugas.surat-keluar.create', compact('kodeSurat', 'nomorUrut', 'lastNomorUrut'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_surat'     => 'required|string|max:255|unique:surat_keluars',
            'nomor_urut'     => 'required|string|max:10', 
            'format_surat'   => 'required|string|max:255',
            'lampiran'       => 'required|string|max:255',
            'tanggal_keluar' => 'required|date',
            'tujuan'         => 'required|string|max:255',
            'perihal'        => 'required|string|max:255',
            'file'           => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:3072',
            'keterangan'     => 'nullable|string',
        ]);

        // Gabungkan nomor urut dan format surat
        $nomorSurat = $validated['nomor_urut'] . '/' . $validated['format_surat'];

        // Validasi nomor surat tidak duplikat
        if (NomorSuratService::isNomorUrutExists($validated['nomor_urut'])) {
            return back()->withInput()->withErrors([
                'nomor_urut' => 'Nomor urut ini sudah digunakan. Silakan gunakan nomor lain.'
            ]);
        }

        $filePath = $request->file('file')->store('surat-keluar', 'public');

        SuratKeluar::create([
            'kode_surat'     => $validated['kode_surat'],
            'nomor_surat'    => $nomorSurat,
            'lampiran'       => $validated['lampiran'],
            'tanggal_keluar' => $validated['tanggal_keluar'],
            'tujuan'         => $validated['tujuan'],
            'perihal'        => $validated['perihal'],
            'file_path'      => $filePath,
            'keterangan'     => $validated['keterangan'],
        ]);

        return redirect()->route('petugas.surat-keluar.index')
            ->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    public function show(SuratKeluar $suratKeluar)
    {
        return view('petugas.surat-keluar.show', compact('suratKeluar'));
    }

    public function edit(SuratKeluar $suratKeluar)
    {
        // Extract nomor urut dari nomor_surat
        $nomorParts = explode('/', $suratKeluar->nomor_surat);
        $nomorUrut = $nomorParts[0] ?? '';
        $formatSurat = $nomorParts[1] ?? '';
        
        $lastNomorUrut = NomorSuratService::getLastNomorUrut();

        return view('petugas.surat-keluar.edit', compact('suratKeluar', 'nomorUrut', 'formatSurat', 'lastNomorUrut'));
    }

    public function update(Request $request, SuratKeluar $suratKeluar)
    {
        $rules = [
            'kode_surat'     => 'required|string|max:255|unique:surat_keluars,kode_surat,'.$suratKeluar->id,
            'nomor_urut'     => 'required|string|max:10',
            'format_surat'   => 'required|string|max:255',
            'lampiran'       => 'required|string|max:255',
            'tanggal_keluar' => 'required|date',
            'tujuan'         => 'required|string|max:255',
            'perihal'        => 'required|string|max:255',
            'keterangan'     => 'nullable|string',
        ];

        // Validasi file hanya jika diupload
        if ($request->hasFile('file')) {
            $rules['file'] = 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:3072';
        }

        $validated = $request->validate($rules);

        // Gabungkan nomor urut dan format surat
        $nomorSurat = $validated['nomor_urut'] . '/' . $validated['format_surat'];

        // Validasi nomor surat tidak duplikat (kecuali untuk record ini)
        $existingNomor = SuratKeluar::where('nomor_surat', $nomorSurat)
            ->where('id', '!=', $suratKeluar->id)
            ->exists();

        if ($existingNomor) {
            return back()->withInput()->withErrors([
                'nomor_urut' => 'Nomor surat ini sudah digunakan. Silakan gunakan nomor lain.'
            ]);
        }

        $data = [
            'kode_surat'     => $validated['kode_surat'],
            'nomor_surat'    => $nomorSurat,
            'lampiran'       => $validated['lampiran'],
            'tanggal_keluar' => $validated['tanggal_keluar'],
            'tujuan'         => $validated['tujuan'],
            'perihal'        => $validated['perihal'],
            'keterangan'     => $validated['keterangan'],
        ];

        // Handle file upload jika ada
        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($suratKeluar->file_path && Storage::disk('public')->exists($suratKeluar->file_path)) {
                Storage::disk('public')->delete($suratKeluar->file_path);
            }
            
            // Simpan file baru
            $data['file_path'] = $request->file('file')->store('surat-keluar', 'public');
        }

        $suratKeluar->update($data);

        return redirect()->route('petugas.surat-keluar.index')
            ->with('success', 'Surat keluar berhasil diperbarui.');
    }

    public function destroy(SuratKeluar $suratKeluar)
    {
        try {
            $nomorSurat = $suratKeluar->nomor_surat;
            Log::info("Memulai penghapusan SuratKeluar ID: {$suratKeluar->id}");
            
            // Hapus file dari storage
            if ($suratKeluar->file_path && Storage::disk('public')->exists($suratKeluar->file_path)) {
                Storage::disk('public')->delete($suratKeluar->file_path);
                Log::info("File PDF dihapus: {$suratKeluar->file_path}");
            }
            
            // Hapus record dari database
            $suratKeluar->delete();
            
            Log::info("Penghapusan SuratKeluar ID: {$suratKeluar->id} selesai");

            return redirect()->route('petugas.surat-keluar.index')
                ->with('success', 'Surat keluar berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal menghapus SuratKeluar: " . $e->getMessage());
            return redirect()->route('petugas.surat-keluar.index')
                ->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }
}