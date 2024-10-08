<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpl';

    protected $fillable = [
        'nama_cpl',
        'kurikulum_id',
        'cpl_ke',
        'deskripsi',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    // Relasi many-to-many dengan model MK melalui tabel pivot cpl_mk
    public function mks()
    {
        return $this->belongsToMany(Mk::class, 'cpl_mk', 'cpl_id', 'mk_id')->withPivot('bobot');
    }

    public function indikators()
    {
        return $this->hasMany(CplIndikator::class, 'cpl_id');
    }
}
