<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_surat',
        'nomor_surat',
        'lampiran',
        'tanggal_masuk',
        'pengirim',
        'perihal',
        'file_path',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /**
     * Relasi ke tabel Arsip
     */
    public function arsip(): HasOne
    {
        return $this->hasOne(Arsip::class, 'nomor_surat', 'nomor_surat')
                   ->where('jenis_surat', 'masuk');
    }
}