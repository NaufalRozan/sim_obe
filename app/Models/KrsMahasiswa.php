<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KrsMahasiswa extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'krs_mahasiswa';
    protected $fillable = [
        'mk_ditawarkan_id',
        'user_id',
    ];

    public function mkDitawarkan()
    {
        return $this->belongsTo(MkDitawarkan::class, 'mk_ditawarkan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cpmkMahasiswa()
    {
        return $this->hasMany(CpmkMahasiswa::class, 'krs_mahasiswa_id');
    }

    public function cpmk()
    {
        return $this->hasManyThrough(Cpmk::class, CpmkMahasiswa::class, 'krs_mahasiswa_id', 'id', 'id', 'cpmk_id');
    }
}
