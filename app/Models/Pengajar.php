<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'pengajar';
    protected $fillable = [
        'user_id',
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function mkDitawarkanHasPengajars()
    {
        return $this->hasMany(MkDitawarkanHasPengajar::class, 'pengajar_id', 'id');
    }

    public function mkDitawarkan()
    {
        return $this->belongsToMany(MkDitawarkan::class, 'mk_ditawarkan_pengajar', 'pengajar_id', 'mk_ditawarkan_id');
    }
}
