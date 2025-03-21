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
        return $this->belongsToMany(Mk::class, 'cpl_mk', 'cpl_id', 'mk_id');
    }

    //bahan kajian yaitu bk
    public function bks()
    {
        return $this->belongsToMany(BahanKajian::class, 'cpl_bk', 'cpl_id', 'bk_id');
    }

    //cplhasbk
    public function cplHasBk()
    {
        return $this->hasMany(CplHasBk::class, 'cpl_id', 'id');
    }
    public function pls()
    {
        return $this->belongsToMany(Pl::class, 'cpl_pl', 'cpl_id', 'pl_id');
    }
}
