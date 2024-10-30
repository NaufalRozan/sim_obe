<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Filament\Panel;


class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim',
        'nip'
    ];

    public function prodis()
    {
        return $this->belongsToMany(Prodi::class, 'user_prodi', 'user_id', 'prodi_id');
    }

    public function krsMahasiswas()
    {
        return $this->hasMany(KrsMahasiswa::class, 'user_id', 'id');
    }

    public function pengajar()
    {
        return $this->hasOne(Pengajar::class, 'user_id');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Akses panel pengajar untuk Dosen dan Staf
        if ($panel->getId() === 'pengajar') {
            return in_array($this->role, ['Dosen', 'Staf']);
        }

        // Akses panel admin untuk Prodi dan Mahasiswa
        if ($panel->getId() === 'admin') {
            return in_array($this->role, ['Prodi', 'Mahasiswa']);
        }

        // Selain itu, tolak akses
        return false;
    }
}
