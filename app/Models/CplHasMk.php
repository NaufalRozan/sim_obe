<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplHasMk extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpl_mk';
    protected $guarded = [];

    public function mk()
    {
        return $this->belongsTo(Mk::class, 'mk_id', 'id');
    }

    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'cpl_id', 'id');
    }
}
