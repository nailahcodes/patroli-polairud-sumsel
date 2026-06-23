<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogistikPatroli extends Model
{
    protected $fillable = [
        'patroli_id',
        'jenis',
        'jumlah',
        'satuan',
        'harga',
        'total',
    ];
}