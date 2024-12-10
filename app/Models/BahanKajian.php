<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanKajian extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'bahan_kajian';

    protected $fillable = [
        'kode_bk',
        'nama_bk',
        'acuan',
    ];

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_bk', 'bk_id', 'cpl_id');
    }


    public function cplHasBk()
    {
        return $this->hasMany(CplHasBk::class, 'bk_id', 'id');
    }
}
