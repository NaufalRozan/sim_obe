<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplHasBk extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpl_bk';

    protected $fillable = [
        'cpl_id',
        'bk_id'
    ];

    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'cpl_id', 'id');
    }

    public function bk()
    {
        return $this->belongsTo(BahanKajian::class, 'bk_id', 'id');
    }
}
