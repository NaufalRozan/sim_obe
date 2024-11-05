<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MkDitawarkan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'mk_ditawarkan';
    protected $fillable = [
        'semester_id',
        'mk_id',
        'rps',
        'kelas',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function mk()
    {
        return $this->belongsTo(Mk::class, 'mk_id', 'id');
    }

    public function cpmks()
    {
        return $this->hasManyThrough(
            Cpmk::class,        // Model tujuan akhir
            CplHasMk::class,    // Model perantara
            'mk_id',            // Foreign key di CplHasMk yang menghubungkan ke Mk
            'cpl_mk_id',        // Foreign key di Cpmk yang menghubungkan ke CplHasMk
            'mk_id',            // Local key di MkDitawarkan
            'id'                // Local key di CplHasMk
        );
    }

    public function krsMahasiswas()
    {
        return $this->hasMany(KrsMahasiswa::class, 'mk_ditawarkan_id', 'id');
    }

    public function pengajars()
    {
        return $this->belongsToMany(Pengajar::class, 'mk_ditawarkan_pengajar', 'mk_ditawarkan_id', 'pengajar_id');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'mk_ditawarkan_id', 'id');
    }
}
