<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kronologis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patroli_id')->constrained('patrolis')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_wib');
            $table->string('jenis_kegiatan');
            $table->string('titik_koordinat')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();

            $table->string('nama_kapal_diperiksa')->nullable();
            $table->string('nahkoda')->nullable();
            $table->string('asal_tujuan')->nullable();
            $table->string('muatan')->nullable();
            $table->text('keterangan_pemeriksaan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kronologis');
    }
};