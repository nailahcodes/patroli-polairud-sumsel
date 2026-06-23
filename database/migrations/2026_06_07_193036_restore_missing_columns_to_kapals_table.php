<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kapals', function (Blueprint $table) {

            $table->string('kelompok')
                ->nullable()
                ->after('kode_kapal');

            $table->string('zona_patroli')
                ->nullable()
                ->after('kelompok');

            $table->string('wilayah_patroli')
                ->nullable()
                ->after('zona_patroli');

            $table->string('komandan_kapal')
                ->nullable()
                ->after('wilayah_patroli');

        });
    }

    public function down(): void
    {
        Schema::table('kapals', function (Blueprint $table) {

            $table->dropColumn([
                'kelompok',
                'zona_patroli',
                'wilayah_patroli',
                'komandan_kapal',
            ]);

        });
    }
};