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
}
