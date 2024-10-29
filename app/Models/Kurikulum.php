<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'kurikulums';
    protected $fillable = [
        'nama_kurikulum',
        'prodi_id',
        'status',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function cpls()
    {
        return $this->hasMany(Cpl::class, 'kurikulum_id');
    }

    // Relasi One-to-Many dengan MK
    public function mks()
    {
        return $this->hasMany(Mk::class, 'kurikulum_id');
    }
}
