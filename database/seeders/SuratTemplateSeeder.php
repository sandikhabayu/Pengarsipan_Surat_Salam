<?php

namespace Database\Seeders;

use App\Models\SuratTemplate;
use Illuminate\Database\Seeder;

class SuratTemplateSeeder extends Seeder
{
    public function run()
    {
        SuratTemplate::create([
            'nama_template' => 'Template Surat Resmi',
            'konten_template' => 'Konten template dasar...'
        ]);
    }
}