<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abk_kronologis', function (Blueprint $table) {

            $table->id();

            $table->foreignId('abk_laporan_id')
                ->constrained('abk_laporans')
                ->cascadeOnDelete();

            $table->text('uraian');

            $table->timestamp('waktu_input');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abk_kronologis');
    }
};