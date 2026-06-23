<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPatroli extends Model
{
    protected $fillable = [
        'patroli_id',
        'jenis_dokumen',
        'periode',
        'tanggal_dokumen',
        'file_path',
        'status_verifikasi',
        'keterangan',
    ];
}