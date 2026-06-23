<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abk_anevs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patroli_id')
                ->constrained('patrolis')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('pembuat_laporan_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('hambatan')->nullable();
            $table->text('kendala')->nullable();

            $table->string('foto_anev')->nullable();

            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abk_anevs');
    }
};