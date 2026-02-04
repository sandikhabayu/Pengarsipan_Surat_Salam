<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluars';

    protected $fillable = [
        'kode_surat',
        'nomor_surat',
        'lampiran',
        'tanggal_keluar',
        'tujuan',
        'perihal',
        'jenis_surat',
        'file_path',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    // Flag untuk mencegah circular deletion
    protected static $isDeleting = false;

    /**
     * Relasi ke tabel Arsip
     */
    public function arsip(): HasOne
    {
        return $this->hasOne(Arsip::class, 'nomor_surat', 'nomor_surat')
                   ->where('jenis_surat', 'keluar');
    }

    /**
     * Event sinkronisasi otomatis untuk penghapusan - PERBAIKAN UTAMA
     */
    protected static function booted()
    {
        // Event ketika SuratKeluar dihapus
        static::deleting(function ($surat) {
            try {
                // Cegah circular deletion
                if (static::$isDeleting) {
                    return;
                }

                static::$isDeleting = true;
                Log::info("Menghapus SuratTemplate terkait untuk SuratKeluar ID: {$surat->id}");

                // Cari dan hapus SuratTemplate dengan nomor_surat yang sama
                $suratTemplate = SuratTemplate::where('nomor_surat', $surat->nomor_surat)->first();
                
                if ($suratTemplate) {
                    Log::info("Menemukan SuratTemplate ID: {$suratTemplate->id} untuk dihapus");
                    
                    // Non-aktifkan event listener sementara untuk mencegah circular deletion
                    SuratTemplate::withoutEvents(function () use ($suratTemplate) {
                        $suratTemplate->delete();
                    });
                    
                    Log::info("SuratTemplate berhasil dihapus");
                } else {
                    Log::info("Tidak ditemukan SuratTemplate dengan nomor: {$surat->nomor_surat}");
                }

            } catch (\Exception $e) {
                Log::error("Gagal menghapus SuratTemplate terkait: " . $e->getMessage());
            } finally {
                static::$isDeleting = false;
            }
        });
    }
}