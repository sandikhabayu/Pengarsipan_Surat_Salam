<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Query untuk Surat Masuk dengan filter bulan dan tahun
        $suratMasuks = SuratMasuk::query()
            ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_masuk', $bulan)
                      ->whereYear('tanggal_masuk', $tahun);
            })
            ->when($bulan && !$tahun, function ($query) use ($bulan) {
                $query->whereMonth('tanggal_masuk', $bulan);
            })
            ->when(!$bulan && $tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal_masuk', $tahun);
            })
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        // Query untuk Surat Keluar dengan filter bulan dan tahun
        $suratKeluars = SuratKeluar::query()
            ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_keluar', $bulan)
                      ->whereYear('tanggal_keluar', $tahun);
            })
            ->when($bulan && !$tahun, function ($query) use ($bulan) {
                $query->whereMonth('tanggal_keluar', $bulan);
            })
            ->when(!$bulan && $tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal_keluar', $tahun);
            })
            ->orderBy('tanggal_keluar', 'desc')
            ->get();

        return view('petugas.laporan.index', compact('suratMasuks', 'suratKeluars', 'bulan', 'tahun'));
    }
    public function indexKepalaSekolah(Request $request)
{
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');

    // Query untuk Surat Masuk
    $suratMasuks = SuratMasuk::query()
        ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
            $query->whereMonth('tanggal_masuk', $bulan)
                  ->whereYear('tanggal_masuk', $tahun);
        })
        ->when($bulan && !$tahun, function ($query) use ($bulan) {
            $query->whereMonth('tanggal_masuk', $bulan);
        })
        ->when(!$bulan && $tahun, function ($query) use ($tahun) {
            $query->whereYear('tanggal_masuk', $tahun);
        })
        ->orderBy('tanggal_masuk', 'desc')
        ->get();

    // Query untuk Surat Keluar
    $suratKeluars = SuratKeluar::query()
        ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
            $query->whereMonth('tanggal_keluar', $bulan)
                  ->whereYear('tanggal_keluar', $tahun);
        })
        ->when($bulan && !$tahun, function ($query) use ($bulan) {
            $query->whereMonth('tanggal_keluar', $bulan);
        })
        ->when(!$bulan && $tahun, function ($query) use ($tahun) {
            $query->whereYear('tanggal_keluar', $tahun);
        })
        ->orderBy('tanggal_keluar', 'desc')
        ->get();
    
    // Hitung total surat
    $totalMasuk = $suratMasuks->count();
    $totalKeluar = $suratKeluars->count();
    
    // Ambil surat terbaru
    $suratTerbaru = SuratMasuk::latest('tanggal_masuk')
                    ->orWhere(function($query) {
                        $query->latest('tanggal_keluar');
                    })
                    ->first();

    return view('kepala-sekolah.laporan.index', compact(
        'suratMasuks', 
        'suratKeluars', 
        'totalMasuk', 
        'totalKeluar', 
        'suratTerbaru',
        'bulan', 
        'tahun'
    ));

    // Ambil surat masuk terbaru
    $terbaruMasuk = SuratMasuk::latest('tanggal_masuk')->first();
    // Ambil surat keluar terbaru
    $terbaruKeluar = SuratKeluar::latest('tanggal_keluar')->first();

    // Bandingkan mana yang lebih baru
    if ($terbaruMasuk && $terbaruKeluar) {
        $suratTerbaru = $terbaruMasuk->tanggal_masuk > $terbaruKeluar->tanggal_keluar 
                        ? $terbaruMasuk 
                        : $terbaruKeluar;
    } elseif ($terbaruMasuk) {
        $suratTerbaru = $terbaruMasuk;
    } elseif ($terbaruKeluar) {
        $suratTerbaru = $terbaruKeluar;
    } else {
        $suratTerbaru = null;
    }
}

    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $namaBulan = $bulan ? DateTime::createFromFormat('!m', $bulan)->format('F') : null;

        // Query untuk Surat Masuk
        $suratMasuks = SuratMasuk::query()
            ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_masuk', $bulan)
                      ->whereYear('tanggal_masuk', $tahun);
            })
            ->when($bulan && !$tahun, function ($query) use ($bulan) {
                $query->whereMonth('tanggal_masuk', $bulan);
            })
            ->when(!$bulan && $tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal_masuk', $tahun);
            })
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        // Query untuk Surat Keluar
        $suratKeluars = SuratKeluar::query()
            ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_keluar', $bulan)
                      ->whereYear('tanggal_keluar', $tahun);
            })
            ->when($bulan && !$tahun, function ($query) use ($bulan) {
                $query->whereMonth('tanggal_keluar', $bulan);
            })
            ->when(!$bulan && $tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal_keluar', $tahun);
            })
            ->orderBy('tanggal_keluar', 'desc')
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('petugas.laporan.pdf', [
            'suratMasuks' => $suratMasuks,
            'suratKeluars' => $suratKeluars,
            'bulan' => $namaBulan,
            'tahun' => $tahun,
            'filterApplied' => $bulan || $tahun
        ]);

        $filename = 'Laporan_Surat';
        if ($bulan && $tahun) {
            $filename .= '_' . $namaBulan . '_' . $tahun;
        } elseif ($tahun) {
            $filename .= '_Tahun_' . $tahun;
        } elseif ($bulan) {
            $filename .= '_Bulan_' . $namaBulan;
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
}