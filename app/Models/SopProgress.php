<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SopProgress extends Model
{
    protected $table = 'sop_progress';

    protected $fillable = [
        'patroli_id',
        'sop_id',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'catatan',
        'checklist_sarpras',
        'bukti_file',
        'bukti_file_2',
        'air_tawar_file',
        'nihil_gelar_perkara',
        'user_id',
    ];

    protected $casts = [
        'checklist_sarpras' => 'array',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'nihil_gelar_perkara' => 'boolean',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    public function patroli()
    {
        return $this->belongsTo(Patroli::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBuktiFileUrlAttribute()
    {
        return $this->bukti_file ? asset('storage/' . $this->bukti_file) : null;
    }

    public function getBuktiFile2UrlAttribute()
    {
        return $this->bukti_file_2 ? asset('storage/' . $this->bukti_file_2) : null;
    }

    public function getAirTawarFileUrlAttribute()
    {
        return $this->air_tawar_file ? asset('storage/' . $this->air_tawar_file) : null;
    }
}