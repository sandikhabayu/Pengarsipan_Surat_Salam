<?php

namespace App\Services;

use App\Models\SuratTemplate;
use App\Models\SuratKeluar;

class NomorSuratService
{
    public static function generateNomorUrut()
    {
        // Cari nomor urut terbesar dari kedua tabel
        $lastTemplate = SuratTemplate::latest()->first();
        $lastKeluar = SuratKeluar::latest()->first();
        
        $lastNumbers = [];
        
        // Extract angka dari nomor_surat template
        if ($lastTemplate && $lastTemplate->nomor_surat) {
            preg_match('/^(\d+)/', $lastTemplate->nomor_surat, $matches);
            if (isset($matches[1])) {
                $lastNumbers[] = (int) $matches[1];
            }
        }
        
        // Extract angka dari nomor_surat keluar
        if ($lastKeluar && $lastKeluar->nomor_surat) {
            preg_match('/^(\d+)/', $lastKeluar->nomor_surat, $matches);
            if (isset($matches[1])) {
                $lastNumbers[] = (int) $matches[1];
            }
        }
        
        // Cari yang terbesar, jika tidak ada mulai dari 1
        $lastNumber = !empty($lastNumbers) ? max($lastNumbers) : 0;
        
        return str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    // Method baru untuk mendapatkan nomor urut terbesar
    public static function getLastNomorUrut()
    {
        $lastTemplate = SuratTemplate::latest()->first();
        $lastKeluar = SuratKeluar::latest()->first();
        
        $lastNumbers = [];
        
        if ($lastTemplate && $lastTemplate->nomor_surat) {
            preg_match('/^(\d+)/', $lastTemplate->nomor_surat, $matches);
            if (isset($matches[1])) {
                $lastNumbers[] = (int) $matches[1];
            }
        }
        
        if ($lastKeluar && $lastKeluar->nomor_surat) {
            preg_match('/^(\d+)/', $lastKeluar->nomor_surat, $matches);
            if (isset($matches[1])) {
                $lastNumbers[] = (int) $matches[1];
            }
        }
        
        return !empty($lastNumbers) ? max($lastNumbers) : 0;
    }

    // Method untuk validasi nomor urut tidak duplikat
    public static function isNomorUrutExists($nomorUrut)
    {
        $nomorSuratPattern = $nomorUrut . '/%';
        
        $existsInTemplate = SuratTemplate::where('nomor_surat', 'like', $nomorSuratPattern)->exists();
        $existsInKeluar = SuratKeluar::where('nomor_surat', 'like', $nomorSuratPattern)->exists();
        
        return $existsInTemplate || $existsInKeluar;
    }
}