<?php

namespace Database\Seeders;

use App\Models\Kapal;
use App\Models\Sop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Administrator',
            'nrp' => '000000',
            'pangkat' => '-',
            'jabatan' => 'Admin Sistem',
            'role' => 'admin',
            'status' => 'aktif',
            'password' => Hash::make('password'),
        ]);

        $kapals = [
            ['Kelompok I', 'V-3002', 'Zona 9', 'Kepayang', 'Aipda Nandi Zaidan'],
            ['Kelompok I', 'V-2012', 'Zona 1', '30 Ilir-Lematang', 'Aipda Rio Widhi'],
            ['Kelompok I', 'V-3005', 'Zona 13', 'Sungai Baung', 'Aipda Zulkifli'],
            ['Kelompok I', 'V-3003', 'Zona 5', 'Sungai Buah', 'Bripka Aprisandi'],
            ['Kelompok II', 'V-4001', 'Zona 12', 'Penuguan', 'AKP Imam Shokibi'],
            ['Kelompok II', 'V-3004', 'Zona 4', 'Sungsang', 'Aipda M. Barus'],
            ['Kelompok II', 'V-2016', 'Zona 7', 'Muara Lalan', 'Aipda Abdullah'],
            ['Kelompok II', 'V-2009', 'Zona 6', 'Sujian', 'Aipda Romi Ali'],
            ['Kelompok III', 'V-2013', 'Zona 10', 'Sembilang', 'Aipda Sunarto'],
            ['Kelompok III', 'V-2008', 'Zona 11', 'Sei Benu', 'Aipda Aprin Sarwanto'],
            ['Kelompok III', 'V-3001', 'Zona 8', 'Primer 13', 'Aipda Abdul Effendi'],
            ['Kelompok III', 'V-2017', 'Zona 2', 'Muara Kumbang-Upang', 'Bripka Heriyanto Ismail'],
        ];

        foreach ($kapals as $kapal) {
            Kapal::create([
                'kelompok' => $kapal[0],
                'kode_kapal' => $kapal[1],
                'zona_patroli' => $kapal[2],
                'wilayah_patroli' => $kapal[3],
                'komandan_kapal' => $kapal[4],
            ]);
        }

        $sops = [
            [1, 'Identifikasi Potensi Ambang Gangguan di masyarakat perairan', 'Kasubdit Patroliairud', 'Kirka interlair', '15 menit', 'Ambang gangguan kamtibmas teridentifikasi'],
            [2, 'Menentukan wilayah kerawanan tugas', 'Kasipatwalairud', 'Zona kerawanan', '5 menit', 'Komandan kapal mengetahui daerah rawan tindak pidana dan laka air'],
            [3, 'Menunjuk personel yang akan melaksanakan giat patroli', 'Kanit Sipatwalairud', 'Sprin patroli', '5 menit', 'ABK mengetahui penunjukan giat patroli'],
            [4, 'Melengkapi administrasi', 'Banit Sipatwalairud', 'Sprin patroli dan berita acara', '1 jam', 'Sprin sudah dibuat'],
            [5, 'Mempersiapkan sarana dan prasarana', 'Komandan Kapal', 'Kapal polisi, alat navigasi, alat keselamatan, BBM', '10 menit', 'Kapal laik laut'],
            [6, 'Memberikan APP sebelum berangkat pelaksanaan kegiatan patroli', 'Kasipatwalairud', 'Speaker aktif dan megaphone', '10 menit', 'ABK memahami tugas masing-masing'],
            [7, 'Kapal polisi bertolak ke daerah yang dituju sesuai surat perintah', 'Komandan Kapal', 'Administrasi sprin, kapal polisi', '8 hari', 'ABK melaksanakan tugas dengan baik'],
            [8, 'Kapal polisi tiba ke daerah yang dituju sesuai surat perintah', 'Komandan Kapal', 'Administrasi sprin, kapal polisi', '8 hari', 'ABK melaksanakan tugas dengan baik'],
            [9, 'Melakukan pemeriksaan dokumen kapal dan pengguna kendaraan air', 'Komandan Kapal', 'Sprin, life jacket, buku panduan, masker, sarung tangan, hand sanitizer', '10 menit', 'Pemeriksaan berjalan lancar'],
            [10, 'Membawa/mengawal kapal ke dermaga kesatuan terdekat', 'Komandan Kapal', 'Kapal polisi', '5 jam', 'Terduga pelanggar tindak pidana tidak melarikan diri'],
            [11, 'Melakukan gelar perkara', 'Komandan Kapal', 'White board, spidol', '1 jam', 'Menentukan terduga pelanggar tindak pidana'],
            [12, 'Menyerahkan berkas pemeriksaan awal ke penyidik kesatuan terdekat', 'Komandan Kapal', 'Berkas', '15 menit', 'Berkas sudah diserahkan ke penyidik'],
            [13, 'Selesai pelaksanaan patroli', 'Komandan Kapal', 'Kapal polisi', '7 hari', 'Kegiatan patroli terlaksana dengan baik'],
            [14, 'Membuat laporan hasil pelaksanaan patroli', 'ABK Kapal', 'Komputer dan alat cetak', '180 menit', 'Laporan telah dibuat'],
            [15, 'Membuat anev kegiatan patroli perairan', 'ABK Kapal', 'Komputer dan alat cetak', '60 menit', 'Anev telah dibuat'],
            [16, 'Mengarsipkan dokumen', 'ABK Kapal', 'Tempat arsip', '5 menit', 'Tertib arsip dokumen'],
        ];

        foreach ($sops as $sop) {
            Sop::create([
                'urutan' => $sop[0],
                'tahapan' => $sop[1],
                'pelaksana' => $sop[2],
                'kelengkapan' => $sop[3],
                'waktu' => $sop[4],
                'output' => $sop[5],
            ]);
        }
    }
}