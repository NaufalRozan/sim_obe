<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mk extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mk';

    protected $fillable = [
        'kode',
        'kurikulum_id',
        'semester',
        'nama_mk',
        'bobot'
    ];

    // Relasi many-to-many dengan model CPL melalui tabel pivot cpl_mk
    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_mk', 'mk_id', 'cpl_id');
    }

    // Relasi dengan Kurikulum (One-to-Many)
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }
}
