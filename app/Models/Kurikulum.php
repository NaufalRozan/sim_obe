<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'kurikulums';
    protected $fillable = [
        'nama_kurikulum',
        'prodi_id',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function cpls()
    {
        return $this->hasMany(Cpl::class, 'kurikulum_id');
    }
}
