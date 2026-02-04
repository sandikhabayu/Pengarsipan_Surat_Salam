<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratKeluar;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratTemplate extends Model
{
    use HasFactory;

    protected $table = 'surat_templates';

    protected $fillable = [
        'jenis_surat',
        'nomor_surat',
        'format_surat',
        'lampiran',
        'tanggal',
        'kepada',
        'perihal',
        'isi_surat',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Flag untuk mencegah circular deletion
    protected static $isDeleting = false;

    // Scope untuk filter jenis surat
    public function scopeJenis($query, $jenis)
    {
        if ($jenis) {
            return $query->where('jenis_surat', $jenis);
        }
        return $query;
    }

    // Getter untuk label jenis surat
    public function getJenisSuratLabelAttribute()
    {
        $jenis = [
            'kepala_desa' => 'Surat Kepala Desa',
            'sekretariat' => 'Surat Sekretariat'
        ];
        
        return $jenis[$this->jenis_surat] ?? $this->jenis_surat;
    }
    
    /**
     * Event sinkronisasi otomatis ke surat keluar
     */
    protected static function booted()
    {
        static::created(function ($surat) {
            try {
                Log::info("Sinkronisasi SuratTemplate -> SuratKeluar dimulai untuk ID {$surat->id}");

                // Generate PDF otomatis
                $filename = 'surat-template-' . $surat->id . '-' . time() . '.pdf';
                $filePath = 'surat-keluar/' . $filename;

                $pdf = Pdf::loadView('petugas.surat-template.pdf', [
                    'surats' => $surat,
                    'isiFormatted' => $isiFormatted,
                     'jenis_surat' => $surat->jenis_surat  // TAMBAHKAN INI
                ])->setPaper('a4', 'portrait');

                Storage::disk('public')->put($filePath, $pdf->output());
                Log::info("PDF berhasil dibuat di {$filePath}");

                $lastSurat = SuratKeluar::latest()->first();
                $lastNumber = $lastSurat ? (int) substr($lastSurat->kode_surat, -2) : 0;
                $kodeSurat = 'SK-'. str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

                // Buat atau update entri di surat_keluars
                SuratKeluar::updateOrCreate(
                    ['nomor_surat' => $surat->nomor_surat],
                    [
                        'kode_surat' => $kodeSurat,
                        'nomor_surat' => $surat->nomor_surat,
                        'lampiran' => $surat->lampiran,
                        'tanggal_keluar' => $surat->tanggal,
                        'tujuan' => $surat->kepada,
                        'perihal' => $surat->perihal,
                        'jenis_surat' => $surat->jenis_surat,  // TAMBAHKAN INI
                        'file_path' => $filePath,
                        'keterangan' => 'Dibuat dari template surat',
                    ]
                );

                Log::info("Sinkronisasi SuratTemplate -> SuratKeluar BERHASIL untuk {$surat->nomor_surat}");

            } catch (\Throwable $e) {
                Log::error("Gagal sinkronisasi SuratTemplate ke SuratKeluar: " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        });

        // Event ketika SuratTemplate dihapus - PERBAIKAN UTAMA
        static::deleting(function ($surat) {
            try {
                // Cegah circular deletion
                if (static::$isDeleting) {
                    return;
                }

                static::$isDeleting = true;
                Log::info("Menghapus SuratKeluar terkait untuk SuratTemplate ID: {$surat->id}");

                // Cari SuratKeluar dengan nomor_surat yang sama
                $suratKeluar = SuratKeluar::where('nomor_surat', $surat->nomor_surat)->first();
                
                if ($suratKeluar) {
                    Log::info("Menemukan SuratKeluar ID: {$suratKeluar->id} untuk dihapus");
                    
                    // Hapus file PDF jika ada
                    if ($suratKeluar->file_path && Storage::disk('public')->exists($suratKeluar->file_path)) {
                        Storage::disk('public')->delete($suratKeluar->file_path);
                        Log::info("File PDF dihapus: {$suratKeluar->file_path}");
                    }
                    
                    // Non-aktifkan event listener sementara untuk mencegah circular deletion
                    SuratKeluar::withoutEvents(function () use ($suratKeluar) {
                        $suratKeluar->delete();
                    });
                    
                    Log::info("SuratKeluar berhasil dihapus");
                } else {
                    Log::info("Tidak ditemukan SuratKeluar dengan nomor: {$surat->nomor_surat}");
                }

            } catch (\Throwable $e) {
                Log::error("Gagal menghapus SuratKeluar terkait: " . $e->getMessage());
            } finally {
                static::$isDeleting = false;
            }
        });

        // Event ketika SuratTemplate diupdate
        static::updated(function ($surat) {
            try {
                Log::info("Sinkronisasi update SuratTemplate -> SuratKeluar untuk ID {$surat->id}");

                // Update SuratKeluar terkait
                $suratKeluar = SuratKeluar::where('nomor_surat', $surat->getOriginal('nomor_surat'))->first();
                
                if ($suratKeluar) {
                    $suratKeluar->update([
                        'nomor_surat' => $surat->nomor_surat,
                        'lampiran' => $surat->lampiran,
                        'tanggal_keluar' => $surat->tanggal,
                        'tujuan' => $surat->kepada,
                        'perihal' => $surat->perihal,
                    ]);
                    Log::info("SuratKeluar berhasil diupdate untuk nomor {$surat->nomor_surat}");
                }

            } catch (\Throwable $e) {
                Log::error("Gagal update SuratKeluar: " . $e->getMessage());
            }
        });
    }
}