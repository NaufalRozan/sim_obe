<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaPengajar extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mahasiswa_pengajar';

    protected $fillable = [
        'mahasiswa_id',
        'pengajar_id',
    ];

    // Relasi ke mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id', 'id')
            ->where('role', 'Mahasiswa'); // Hanya mengambil pengguna dengan role Mahasiswa
    }

    // Relasi ke pengajar
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'pengajar_id', 'id')
            ->where('role', 'Dosen'); // Hanya mengambil pengguna dengan role Dosen
    }
}
