<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrolis', function (Blueprint $table) {
            if (!Schema::hasColumn('patrolis', 'komandan_id')) {
                $table->foreignId('komandan_id')
                    ->nullable()
                    ->after('kapal_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('patrolis', function (Blueprint $table) {
            if (Schema::hasColumn('patrolis', 'komandan_id')) {
                $table->dropConstrainedForeignId('komandan_id');
            }
        });
    }
};