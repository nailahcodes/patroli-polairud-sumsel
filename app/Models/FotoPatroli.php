<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoPatroli extends Model
{
    protected $fillable = [
        'patroli_id',
        'kategori_foto',
        'tanggal',
        'jam',
        'lokasi',
        'koordinat',
        'deskripsi',
        'file_path',
    ];
}