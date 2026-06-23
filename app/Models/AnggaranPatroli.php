<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggaranPatroli extends Model
{
    protected $fillable = [
        'patroli_id',
        'komponen',
        'jumlah',
        'nominal',
        'total',
    ];
}