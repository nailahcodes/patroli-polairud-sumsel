<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abk_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patroli_id')->constrained('patrolis')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('total_pengisian_bbm', 12, 2)->default(0);
            $table->decimal('total_stock_bbm_tangki', 12, 2)->default(0);
            $table->decimal('total_jarak_tempuh', 12, 2)->default(0);
            $table->decimal('total_pemakaian_bbm', 12, 2)->default(0);
            $table->decimal('pemakaian_bbm_selama_layar', 12, 2)->default(0);
            $table->decimal('kecepatan_rata_rata', 12, 2)->default(0);
            $table->decimal('sisa_bbm_selesai_patroli', 12, 2)->default(0);

            $table->string('status')->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('abk_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abk_laporan_id')->constrained('abk_laporans')->cascadeOnDelete();
            $table->string('komponen');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('abk_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abk_laporan_id')->constrained('abk_laporans')->cascadeOnDelete();
            $table->string('jenis');
            $table->decimal('jumlah_liter', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('abk_koordinats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abk_laporan_id')->constrained('abk_laporans')->cascadeOnDelete();
            $table->string('jenis');
            $table->string('koordinat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('riksa_kapals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abk_laporan_id')->constrained('abk_laporans')->cascadeOnDelete();
            $table->string('nama_kapal')->nullable();
            $table->string('nama_nahkoda')->nullable();
            $table->string('dari_tujuan')->nullable();
            $table->string('muatan')->nullable();
            $table->string('titik_koordinat')->nullable();
            $table->string('kategori')->default('aman'); // aman, tindak_pidana, pelanggaran
            $table->text('penjelasan')->nullable();
            $table->string('foto_riksa')->nullable();
            $table->string('foto_binluh')->nullable();
            $table->string('surat_hasil_pemeriksaan')->nullable();
            $table->timestamps();
        });

        Schema::create('abk_lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abk_laporan_id')->constrained('abk_laporans')->cascadeOnDelete();
            $table->string('jenis');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abk_lampirans');
        Schema::dropIfExists('riksa_kapals');
        Schema::dropIfExists('abk_koordinats');
        Schema::dropIfExists('abk_logistiks');
        Schema::dropIfExists('abk_anggarans');
        Schema::dropIfExists('abk_laporans');
    }
};