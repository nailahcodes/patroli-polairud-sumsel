<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sop_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('sop_progress', 'checklist_sarpras')) {
                $table->json('checklist_sarpras')->nullable()->after('catatan');
            }

            if (! Schema::hasColumn('sop_progress', 'bukti_file')) {
                $table->string('bukti_file')->nullable()->after('checklist_sarpras');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sop_progress', function (Blueprint $table) {
            if (Schema::hasColumn('sop_progress', 'checklist_sarpras')) {
                $table->dropColumn('checklist_sarpras');
            }

            if (Schema::hasColumn('sop_progress', 'bukti_file')) {
                $table->dropColumn('bukti_file');
            }
        });
    }
};