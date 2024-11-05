<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'laporan';
    protected $fillable = [
        'cpmk_id',
        'mk_ditawarkan_id',
        'faktor_pendukung_kendala',
        'rtl',
    ];

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }

    public function mkDitawarkan()
    {
        return $this->belongsTo(MkDitawarkan::class);
    }
}
