<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('surat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('format_surat'); 
            $table->string('lampiran');
            $table->date('tanggal');
            $table->string('kepada');
            $table->string('perihal');
            $table->text('isi_surat');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_templates');
    }
};
