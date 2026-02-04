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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_surat');
            $table->string('nomor_surat');
            $table->string('lampiran');
            $table->date('tanggal_keluar');
            $table->string('tujuan');
            $table->string('perihal');
            $table->string('file_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('surat_keluars');
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
