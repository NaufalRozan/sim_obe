<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplIndikator extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpl_indikator';
    protected $fillable = [
        'cpl_id',
        'kode',
        'deskripsi'
    ];

}
