<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Kurikulum;

class Prodi extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'prodis';
    protected $fillable = [
        'nama_prodi',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_prodi', 'prodi_id', 'user_id');
    }

    public function kurikulums()
    {
        return $this->hasMany(Kurikulum::class);
    }
}
