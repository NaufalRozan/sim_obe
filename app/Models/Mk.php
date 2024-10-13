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
        'rps',
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

    public function cpmks()
    {
        return $this->hasManyThrough(
            Cpmk::class,
            CplHasMk::class,
            'mk_id', // Foreign key di cpl_mk yang merujuk ke mk
            'cpl_mk_id', // Foreign key di cpmk yang merujuk ke cpl_mk
            'id', // Local key di mk
            'id' // Local key di cpl_mk
        );
    }
}
