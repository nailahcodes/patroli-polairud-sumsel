<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('patrolis', 'komandan_id')) {
            Schema::table('patrolis', function (Blueprint $table) {
                $table->dropConstrainedForeignId('komandan_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('patrolis', 'komandan_id')) {
            Schema::table('patrolis', function (Blueprint $table) {
                $table->foreignId('komandan_id')
                    ->nullable()
                    ->after('kapal_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }
};