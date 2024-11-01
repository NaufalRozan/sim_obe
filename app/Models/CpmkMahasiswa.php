<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpmkMahasiswa extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpmk_mahasiswa';
    protected $fillable = [
        'cpmk_id',
        'krs_mahasiswa_id',
        'nilai',
    ];

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmk_id');
    }

    public function krsMahasiswa()
    {
        return $this->belongsTo(KrsMahasiswa::class, 'krs_mahasiswa_id');
    }
}
