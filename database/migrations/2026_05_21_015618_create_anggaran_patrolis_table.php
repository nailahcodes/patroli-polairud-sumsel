<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran_patrolis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patroli_id')->constrained('patrolis')->cascadeOnDelete();
            $table->string('komponen');
            $table->integer('jumlah')->default(0);
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran_patrolis');
    }
};