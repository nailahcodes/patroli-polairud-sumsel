<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_patrolis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patroli_id')->constrained('patrolis')->cascadeOnDelete();
            $table->string('kategori_foto');
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('koordinat')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_patrolis');
    }
};