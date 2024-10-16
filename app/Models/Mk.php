<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mk extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mk';

    protected $fillable = [
        'kode',
        'kurikulum_id',
        'semester',
        'nama_mk',
        'rps',
    ];

    protected static function boot()
    {
        parent::boot();

        // Hapus file saat record MK dihapus
        static::deleting(function ($mk) {
            if ($mk->rps) {
                Storage::delete('rps-files/' . $mk->rps); // Hapus file dari storage
            }
        });

        // Hapus file lama saat RPS di-update
        static::updating(function ($mk) {
            if ($mk->isDirty('rps')) { // Jika ada perubahan pada RPS
                $originalRps = $mk->getOriginal('rps'); // Mendapatkan file RPS yang lama
                if ($originalRps && $originalRps !== $mk->rps) { // Jika ada RPS lama yang berbeda dari yang baru
                    Storage::delete('rps-files/' . $originalRps); // Hapus file RPS lama dari storage
                }
            }
        });
    }

    // Relasi many-to-many dengan model CPL melalui tabel pivot cpl_mk
    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_mk', 'mk_id', 'cpl_id');
    }

    // Relasi dengan Kurikulum (One-to-Many)
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function cpmks()
    {
        return $this->hasManyThrough(
            Cpmk::class,
            CplHasMk::class,
            'mk_id', // Foreign key di cpl_mk yang merujuk ke mk
            'cpl_mk_id', // Foreign key di cpmk yang merujuk ke cpl_mk
            'id', // Local key di mk
            'id' // Local key di cpl_mk
        );
    }
}
