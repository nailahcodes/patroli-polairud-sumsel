<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sop_progress')) {
            return;
        }

        Schema::table('sop_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('sop_progress', 'bukti_file_2')) {
                $table->string('bukti_file_2')->nullable()->after('bukti_file');
            }
            if (!Schema::hasColumn('sop_progress', 'air_tawar_file')) {
                $table->string('air_tawar_file')->nullable()->after('bukti_file_2');
            }
            if (!Schema::hasColumn('sop_progress', 'nihil_gelar_perkara')) {
                $table->boolean('nihil_gelar_perkara')->default(false)->after('air_tawar_file');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sop_progress')) {
            return;
        }

        Schema::table('sop_progress', function (Blueprint $table) {
            if (Schema::hasColumn('sop_progress', 'bukti_file_2')) {
                $table->dropColumn('bukti_file_2');
            }
            if (Schema::hasColumn('sop_progress', 'air_tawar_file')) {
                $table->dropColumn('air_tawar_file');
            }
            if (Schema::hasColumn('sop_progress', 'nihil_gelar_perkara')) {
                $table->dropColumn('nihil_gelar_perkara');
            }
        });
    }
};