<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riksa_kapals', function (Blueprint $table) {

            $table->timestamp('waktu_kejadian')
                ->nullable()
                ->after('penjelasan');

        });
    }

    public function down(): void
    {
        Schema::table('riksa_kapals', function (Blueprint $table) {

            $table->dropColumn('waktu_kejadian');

        });
    }
};