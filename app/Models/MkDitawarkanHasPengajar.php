<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MkDitawarkanHasPengajar extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mk_ditawarkan_pengajar';
    protected $guarded = [];

    public function mkDitawarkan()
    {
        return $this->belongsTo(MkDitawarkan::class, 'mk_ditawarkan_id', 'id');
    }

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'pengajar_id', 'id');
    }
}
