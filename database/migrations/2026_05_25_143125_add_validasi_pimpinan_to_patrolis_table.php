<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrolis', function (Blueprint $table) {
            if (! Schema::hasColumn('patrolis', 'validasi_pimpinan_status')) {
                $table->string('validasi_pimpinan_status')->nullable()->after('status');
            }

            if (! Schema::hasColumn('patrolis', 'validasi_pimpinan_catatan')) {
                $table->text('validasi_pimpinan_catatan')->nullable()->after('validasi_pimpinan_status');
            }

            if (! Schema::hasColumn('patrolis', 'validasi_pimpinan_user_id')) {
                $table->foreignId('validasi_pimpinan_user_id')
                    ->nullable()
                    ->after('validasi_pimpinan_catatan')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('patrolis', 'validasi_pimpinan_at')) {
                $table->dateTime('validasi_pimpinan_at')->nullable()->after('validasi_pimpinan_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patrolis', function (Blueprint $table) {
            if (Schema::hasColumn('patrolis', 'validasi_pimpinan_user_id')) {
                $table->dropConstrainedForeignId('validasi_pimpinan_user_id');
            }

            if (Schema::hasColumn('patrolis', 'validasi_pimpinan_at')) {
                $table->dropColumn('validasi_pimpinan_at');
            }

            if (Schema::hasColumn('patrolis', 'validasi_pimpinan_catatan')) {
                $table->dropColumn('validasi_pimpinan_catatan');
            }

            if (Schema::hasColumn('patrolis', 'validasi_pimpinan_status')) {
                $table->dropColumn('validasi_pimpinan_status');
            }
        });
    }
};