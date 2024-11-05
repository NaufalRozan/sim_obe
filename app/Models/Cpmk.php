<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpmk';

    protected $fillable = [
        'cpl_mk_id',
        'kode_cpmk',
        'deskripsi',
        'bobot',
        'batas_nilai_lulus',
        'batas_nilai_memuaskan',
    ];

    public function cplMk()
    {
        return $this->belongsTo(CplHasMk::class, 'cpl_mk_id');
    }


    public function mk()
    {
        return $this->hasOneThrough(
            Mk::class,
            CplHasMk::class,
            'id', // Foreign key on cpl_mk table...
            'id', // Foreign key on mk table...
            'cpl_mk_id', // Local key on cpmk table...
            'mk_id' // Local key on cpl_mk table...
        );
    }

    public function mkDitawarkan()
    {
        return $this->hasOneThrough(
            MkDitawarkan::class,   // Model tujuan akhir
            CplHasMk::class,       // Model perantara
            'id',                  // Foreign key di CplHasMk yang menghubungkan ke Cpmk
            'mk_id',               // Foreign key di MkDitawarkan yang menghubungkan ke Mk
            'cpl_mk_id',           // Local key di Cpmk
            'mk_id'                // Local key di CplHasMk
        );
    }

    public function cpmkMahasiswa()
    {
        return $this->hasMany(CpmkMahasiswa::class, 'cpmk_id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'cpmk_id');
    }
}
