<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sop_progress_harians')) {
            return;
        }

        Schema::create('sop_progress_harians', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('patroli_harian_id');

            $table->foreignId('sop_id')
                ->constrained('sops')
                ->cascadeOnDelete();

            $table->string('status')
                ->default('belum');

            $table->dateTime('waktu_mulai')
                ->nullable();

            $table->dateTime('waktu_selesai')
                ->nullable();

            $table->text('catatan')
                ->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sop_progress_harians');
    }
};