<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_patrolis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patroli_id')->constrained('patrolis')->cascadeOnDelete();
            $table->string('jenis_dokumen');
            $table->string('periode');
            $table->date('tanggal_dokumen')->nullable();
            $table->string('file_path');
            $table->string('status_verifikasi')->default('belum dicek');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_patrolis');
    }
};