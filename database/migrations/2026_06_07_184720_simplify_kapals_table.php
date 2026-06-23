<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kapals', function (Blueprint $table) {
            if (Schema::hasColumn('kapals', 'kelompok')) {
                $table->dropColumn('kelompok');
            }
            if (Schema::hasColumn('kapals', 'zona_patroli')) {
                $table->dropColumn('zona_patroli');
            }
            if (Schema::hasColumn('kapals', 'wilayah_patroli')) {
                $table->dropColumn('wilayah_patroli');
            }
            if (Schema::hasColumn('kapals', 'komandan_kapal')) {
                $table->dropColumn('komandan_kapal');
            }
        });
    }

    public function down(): void
    {
        //
    }
};