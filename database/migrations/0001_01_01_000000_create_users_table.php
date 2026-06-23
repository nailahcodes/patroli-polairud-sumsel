<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nrp')->unique();
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('role')->default('abk');
            $table->unsignedBigInteger('kapal_id')->nullable();
            $table->string('status')->default('aktif');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};