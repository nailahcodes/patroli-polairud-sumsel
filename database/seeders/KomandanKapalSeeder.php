<?php

namespace Database\Seeders;

use App\Models\Kapal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KomandanKapalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kelompok' => 'Kelompok I',
                'kode_kapal' => 'V-3002',
                'zona_patroli' => 'Zona 9',
                'wilayah_patroli' => 'Kepayang',
                'pangkat' => 'Aipda',
                'nama' => 'Nandi Zaidan',
                'nrp' => 'KMD3002',
            ],
            [
                'kelompok' => 'Kelompok I',
                'kode_kapal' => 'V-2012',
                'zona_patroli' => 'Zona 1',
                'wilayah_patroli' => '30 Ilir-Lematang',
                'pangkat' => 'Aipda',
                'nama' => 'Rio Widhi',
                'nrp' => 'KMD2012',
            ],
            [
                'kelompok' => 'Kelompok I',
                'kode_kapal' => 'V-3005',
                'zona_patroli' => 'Zona 13',
                'wilayah_patroli' => 'Sungai Baung',
                'pangkat' => 'Aipda',
                'nama' => 'Zulkifli',
                'nrp' => 'KMD3005',
            ],
            [
                'kelompok' => 'Kelompok I',
                'kode_kapal' => 'V-3003',
                'zona_patroli' => 'Zona 5',
                'wilayah_patroli' => 'Sungai Buah',
                'pangkat' => 'Bripka',
                'nama' => 'Aprisandi',
                'nrp' => 'KMD3003',
            ],
            [
                'kelompok' => 'Kelompok II',
                'kode_kapal' => 'V-4001',
                'zona_patroli' => 'Zona 12',
                'wilayah_patroli' => 'Penuguan',
                'pangkat' => 'AKP',
                'nama' => 'Imam Shokibi',
                'nrp' => 'KMD4001',
            ],
            [
                'kelompok' => 'Kelompok II',
                'kode_kapal' => 'V-3004',
                'zona_patroli' => 'Zona 4',
                'wilayah_patroli' => 'Sungsang',
                'pangkat' => 'Aipda',
                'nama' => 'M. Barus',
                'nrp' => 'KMD3004',
            ],
            [
                'kelompok' => 'Kelompok II',
                'kode_kapal' => 'V-2016',
                'zona_patroli' => 'Zona 7',
                'wilayah_patroli' => 'Muara Lalan',
                'pangkat' => 'Aipda',
                'nama' => 'Abdullah',
                'nrp' => 'KMD2016',
            ],
            [
                'kelompok' => 'Kelompok II',
                'kode_kapal' => 'V-2009',
                'zona_patroli' => 'Zona 6',
                'wilayah_patroli' => 'Sujian',
                'pangkat' => 'Aipda',
                'nama' => 'Romi Ali',
                'nrp' => 'KMD2009',
            ],
            [
                'kelompok' => 'Kelompok III',
                'kode_kapal' => 'V-2013',
                'zona_patroli' => 'Zona 10',
                'wilayah_patroli' => 'Sembilang',
                'pangkat' => 'Aipda',
                'nama' => 'Sunarto',
                'nrp' => 'KMD2013',
            ],
            [
                'kelompok' => 'Kelompok III',
                'kode_kapal' => 'V-2008',
                'zona_patroli' => 'Zona 11',
                'wilayah_patroli' => 'Sei Benu',
                'pangkat' => 'Aipda',
                'nama' => 'Aprin Sarwanto',
                'nrp' => 'KMD2008',
            ],
            [
                'kelompok' => 'Kelompok III',
                'kode_kapal' => 'V-3001',
                'zona_patroli' => 'Zona 8',
                'wilayah_patroli' => 'Primer 13',
                'pangkat' => 'Aipda',
                'nama' => 'Abdul Effendi',
                'nrp' => 'KMD3001',
            ],
            [
                'kelompok' => 'Kelompok III',
                'kode_kapal' => 'V-2017',
                'zona_patroli' => 'Zona 2',
                'wilayah_patroli' => 'Muara Kumbang-Upang',
                'pangkat' => 'Bripka',
                'nama' => 'Heriyanto Ismail',
                'nrp' => 'KMD2017',
            ],
        ];

        foreach ($data as $item) {
            $user = User::updateOrCreate(
                ['nrp' => $item['nrp']],
                [
                    'nama' => $item['nama'],
                    'pangkat' => $item['pangkat'],
                    'jabatan' => 'Komandan Kapal',
                    'role' => 'komandan',
                    'status' => 'aktif',
                    'password' => Hash::make('password'),
                ]
            );

            $kapal = Kapal::updateOrCreate(
                ['kode_kapal' => $item['kode_kapal']],
                [
                    'kelompok' => $item['kelompok'],
                    'komandan_id' => $user->id,
                    'zona_patroli' => $item['zona_patroli'],
                    'wilayah_patroli' => $item['wilayah_patroli'],
                    'komandan_kapal' => $item['pangkat'] . ' ' . $item['nama'],
                    'status' => 'aktif',
                ]
            );

            $user->update([
                'kapal_id' => $kapal->id,
            ]);
        }
    }
}