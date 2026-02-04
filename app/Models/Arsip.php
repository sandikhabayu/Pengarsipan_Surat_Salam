<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_surat',
        'nomor_surat',
        'jenis_surat',
        'tanggal',
        'pihak_terkait',
        'perihal',
        'file_path'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}