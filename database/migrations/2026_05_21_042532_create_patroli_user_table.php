<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('patroli_user')) {
            Schema::create('patroli_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patroli_id')->constrained('patrolis')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('posisi')->default('ABK Kapal');
                $table->timestamps();

                $table->unique(['patroli_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('patroli_user');
    }
};