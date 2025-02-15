<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tahun_ajaran';
    protected $fillable = [
        'nama_tahun_ajaran',
    ];

    public function semester()
    {
        return $this->hasMany(Semester::class, 'tahun_ajaran_id', 'id');
    }
}
