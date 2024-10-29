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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mkDitawarkanHasPengajars()
    {
        return $this->hasMany(MkDitawarkanHasPengajar::class, 'pengajar_id', 'id');
    }
}
