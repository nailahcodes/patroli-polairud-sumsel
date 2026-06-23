<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    protected $fillable = [
        'urutan',
        'tahapan',
        'pelaksana',
        'kelengkapan',
        'waktu',
        'output',
    ];
}