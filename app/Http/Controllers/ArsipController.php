<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk surat masuk belum diarsipkan dengan filter
        $suratMasukQuery = SuratMasuk::whereDoesntHave('arsip');
        
        // Query untuk surat keluar belum diarsipkan dengan filter
        $suratKeluarQuery = SuratKeluar::whereDoesntHave('arsip');
        
        // Filter surat masuk
        if ($request->search_masuk) {
            $suratMasukQuery->where(function($query) use ($request) {
                $query->where('nomor_surat', 'like', '%'.$request->search_masuk.'%')
                    ->orWhere('pengirim', 'like', '%'.$request->search_masuk.'%')
                    ->orWhere('perihal', 'like', '%'.$request->search_masuk.'%')
                    ->orWhere('kode_surat', 'like', '%'.$request->search_masuk.'%');
            });
        }
        
        if ($request->bulan_masuk) {
            $suratMasukQuery->whereMonth('tanggal_masuk', $request->bulan_masuk);
        }
        
        if ($request->tahun_masuk) {
            $suratMasukQuery->whereYear('tanggal_masuk', $request->tahun_masuk);
        }
        
        // Filter surat keluar
        if ($request->search_keluar) {
            $suratKeluarQuery->where(function($query) use ($request) {
                $query->where('nomor_surat', 'like', '%'.$request->search_keluar.'%')
                    ->orWhere('tujuan', 'like', '%'.$request->search_keluar.'%')
                    ->orWhere('perihal', 'like', '%'.$request->search_keluar.'%')
                    ->orWhere('kode_surat', 'like', '%'.$request->search_keluar.'%');
            });
        }
        
        if ($request->bulan_keluar) {
            $suratKeluarQuery->whereMonth('tanggal_keluar', $request->bulan_keluar);
        }
        
        if ($request->tahun_keluar) {
            $suratKeluarQuery->whereYear('tanggal_keluar', $request->tahun_keluar);
        }
        
        $suratMasuks = $suratMasukQuery->get();
        $suratKeluars = $suratKeluarQuery->get();
        
        // Query untuk arsip dengan filter (seperti sebelumnya)
        $arsipQuery = Arsip::query();
        
        if ($request->jenis_surat) {
            $arsipQuery->where('jenis_surat', $request->jenis_surat);
        }
        
        if ($request->bulan || $request->tahun) {
            $arsipQuery->where(function($query) use ($request) {
                if ($request->bulan) {
                    $query->whereMonth('tanggal', $request->bulan);
                }
                if ($request->tahun) {
                    $query->whereYear('tanggal', $request->tahun);
                }
            });
        }
        
        if ($request->search) {
            $arsipQuery->where(function($query) use ($request) {
                $query->where('nomor_surat', 'like', '%'.$request->search.'%')
                    ->orWhere('pihak_terkait', 'like', '%'.$request->search.'%')
                    ->orWhere('perihal', 'like', '%'.$request->search.'%')
                    ->orWhere('kode_surat', 'like', '%'.$request->search.'%');
            });
        }
        
        $arsips = $arsipQuery->latest()->paginate(10);
        
        return view('petugas.arsip.index', compact('suratMasuks', 'suratKeluars', 'arsips'));
    }

    public function indexKepalaSekolah(Request $request)
    {
        // Mulai query dasar
    $query = Arsip::query();
    
    // FILTER: Jika user memilih jenis surat
    if ($request->jenis_surat) {
        $query->where('jenis_surat', $request->jenis_surat);
    }
    
    // PENCARIAN: Jika user mengisi kolom search
    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('nomor_surat', 'like', '%'.$request->search.'%')
              ->orWhere('pihak_terkait', 'like', '%'.$request->search.'%')
              ->orWhere('perihal', 'like', '%'.$request->search.'%');
        });
    }
    
    // Ambil data dengan pagination
    $arsips = $query->orderBy('tanggal', 'desc')->paginate(10);
    
    return view('kepala-sekolah.arsip.index', compact('arsips'));
    }

    public function arsipkan(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required|in:masuk,keluar',
        ]);

        try {
            if ($request->jenis_surat === 'masuk') {
                $surat = SuratMasuk::findOrFail($id);

                // Cek apakah sudah diarsipkan
                if (Arsip::where('nomor_surat', $surat->nomor_surat)->where('jenis_surat', 'masuk')->exists()) {
                    return redirect()->back()->with('error', 'Surat masuk ini sudah diarsipkan.');
                }

                // Salin file ke folder arsip
                $newFilePath = $this->copyFileToArchive($surat->file_path, 'masuk');

                Arsip::create([
                    'kode_surat'     => $surat->kode_surat,
                    'nomor_surat'    => $surat->nomor_surat,
                    'jenis_surat'    => 'masuk',
                    'tanggal'        => $surat->tanggal_masuk,
                    'pihak_terkait'  => $surat->pengirim,
                    'perihal'        => $surat->perihal,
                    'file_path'      => $newFilePath,
                ]);

            } else {
                $surat = SuratKeluar::findOrFail($id);

                // Cek apakah sudah diarsipkan
                if (Arsip::where('nomor_surat', $surat->nomor_surat)->where('jenis_surat', 'keluar')->exists()) {
                    return redirect()->back()->with('error', 'Surat keluar ini sudah diarsipkan.');
                }

                // Salin file ke folder arsip
                $newFilePath = $this->copyFileToArchive($surat->file_path, 'keluar');

                Arsip::create([
                    'kode_surat'     => $surat->kode_surat,
                    'nomor_surat'    => $surat->nomor_surat,
                    'jenis_surat'    => 'keluar',
                    'tanggal'        => $surat->tanggal_keluar,
                    'pihak_terkait'  => $surat->tujuan,
                    'perihal'        => $surat->perihal,
                    'file_path'      => $newFilePath,
                ]);
            }

            return redirect()->route('petugas.arsip.index')->with('success', 'Surat berhasil diarsipkan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengarsipkan surat: ' . $e->getMessage());
        }
    }

    /**
     * Salin file ke folder arsip
     */
    private function copyFileToArchive($originalFilePath, $jenisSurat)
    {
        if (!$originalFilePath || !Storage::disk('public')->exists($originalFilePath)) {
            throw new \Exception('File surat tidak ditemukan.');
        }

        // Generate nama file baru untuk arsip
        $originalFileName = pathinfo($originalFilePath, PATHINFO_BASENAME);
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $newFileName = 'arsip_' . time() . '_' . uniqid() . '.' . $extension;
        $newFilePath = 'arsip/' . $jenisSurat . '/' . $newFileName;

        // Salin file ke folder arsip
        Storage::disk('public')->copy($originalFilePath, $newFilePath);

        return $newFilePath;
    }

    public function destroy($id)
    {
        try {
            $arsip = Arsip::findOrFail($id);
            $arsip->delete();
            
            return redirect()->route('petugas.arsip.index')
                ->with('success', 'Arsip berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus arsip: ' . $e->getMessage());
        }
    }

     /**
     * Method untuk menampilkan file arsip
     */
    public function showFile($id)
    {
        $arsip = Arsip::findOrFail($id);
        
        if (!$arsip->file_path || !Storage::disk('public')->exists($arsip->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }
        
        $filePath = storage_path('app/public/' . $arsip->file_path);
        $fileName = pathinfo($arsip->file_path, PATHINFO_BASENAME);
        
        return response()->file($filePath, [
            'Content-Type' => Storage::disk('public')->mimeType($arsip->file_path),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }
}