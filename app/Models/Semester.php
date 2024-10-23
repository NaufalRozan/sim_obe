<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'semester';
    protected $fillable = [
        'tahun_ajaran_id',
        'angka_semester',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

    public function mkDitawarkan()
    {
        return $this->hasMany(MkDitawarkan::class, 'semester_id', 'id');
    }
}
