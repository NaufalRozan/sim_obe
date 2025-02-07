<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pl extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'pl';

    protected $fillable = [
        'kurikulum_id',
        'kode',
        'nama_pl',
        'unsur',
        'sumber',
    ];

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_pl', 'pl_id', 'cpl_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }
}
