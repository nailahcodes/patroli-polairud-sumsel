<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrolis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sprin')->nullable();
            $table->foreignId('kapal_id')->constrained('kapals')->cascadeOnDelete();
            $table->string('wilayah_patroli');
            $table->date('tanggal_persiapan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status')->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrolis');
    }
};