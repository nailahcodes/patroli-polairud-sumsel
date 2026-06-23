<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kapals', 'komandan_id')) {
            Schema::table('kapals', function (Blueprint $table) {
                $table->foreignId('komandan_id')
                    ->nullable()
                    ->after('kode_kapal')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kapals', 'komandan_id')) {
            Schema::table('kapals', function (Blueprint $table) {
                $table->dropConstrainedForeignId('komandan_id');
            });
        }
    }
};