<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arsips', function (Blueprint $table) {
            $table->id();
            $table->string('kode_surat'); // ← Tambahkan ini
            $table->string('nomor_surat');
            $table->string('jenis_surat'); // 'masuk' atau 'keluar'
            $table->date('tanggal');
            $table->string('pihak_terkait'); // pengirim atau tujuan
            $table->string('perihal');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};
