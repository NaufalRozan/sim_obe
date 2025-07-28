<?php

namespace App\Imports;

use App\Models\Cpmk;
use App\Models\CpmkMahasiswa;
use App\Models\KrsMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CpmkMahasiswaImport implements ToModel, WithHeadingRow
{
    protected $cpmks;
    protected $mkDitawarkanId;

    public function __construct($mkDitawarkanId)
    {
        $this->mkDitawarkanId = $mkDitawarkanId;

        $this->cpmks = Cpmk::whereHas('cplMk.mkDitawarkan', function ($query) use ($mkDitawarkanId) {
            $query->where('mk_ditawarkan.id', $mkDitawarkanId);
        })->get()->keyBy('kode_cpmk');

        File::append(storage_path('logs/import_debug.log'), "\n--- MULAI IMPORT ---\n");
        File::append(storage_path('logs/import_debug.log'), "CPMK Loaded: " . json_encode($this->cpmks->keys()) . "\n");
    }

    public function model(array $row)
    {
        $nim = trim($row['nim'] ?? '');
        File::append(storage_path('logs/import_debug.log'), "Mengecek NIM: {$nim}\n");

        // Coba cari user
        $user = User::where('nim', $nim)->first();

        // Log semua NIM yang tersedia (sekali saja cukup di awal real case)
        if (!$user) {
            $allNims = User::pluck('nim')->toArray();
            File::append(storage_path('logs/import_debug.log'), "❌ User tidak ditemukan. Semua NIM yang tersedia: " . implode(', ', $allNims) . "\n");
            return null;
        }

        File::append(storage_path('logs/import_debug.log'), "✅ User ditemukan: {$user->name} ({$user->id})\n");

        // Cari KRS
        $krsMahasiswa = KrsMahasiswa::where('user_id', $user->id)
            ->where('mk_ditawarkan_id', $this->mkDitawarkanId)
            ->first();

        if (!$krsMahasiswa) {
            File::append(storage_path('logs/import_debug.log'), "❌ KRS tidak ditemukan untuk user ID: {$user->id}\n");
            return null;
        }

        File::append(storage_path('logs/import_debug.log'), "✅ KRS ditemukan ID: {$krsMahasiswa->id}\n");

        // Loop nilai CPMK
        foreach ($row as $key => $value) {
            if (in_array($key, ['nama_mahasiswa', 'nim']) || $value === null) continue;

            $kodeCpmk = strtoupper(str_replace('_', ' ', $key));
            $kodeCpmk = str_replace('CPMK ', 'CPMK - ', $kodeCpmk);

            $cpmk = $this->cpmks->get($kodeCpmk);

            if (!$cpmk) {
                File::append(storage_path('logs/import_debug.log'), "❌ CPMK tidak ditemukan: {$kodeCpmk}\n");
                continue;
            }

            $result = CpmkMahasiswa::updateOrCreate(
                [
                    'cpmk_id' => $cpmk->id,
                    'krs_mahasiswa_id' => $krsMahasiswa->id,
                ],
                [
                    'nilai' => $value,
                ]
            );

            File::append(storage_path('logs/import_debug.log'), "✅ CPMK {$kodeCpmk} untuk {$nim} disimpan: {$value} → " . ($result->wasRecentlyCreated ? "CREATED" : "UPDATED") . "\n");
        }

        return null;
    }
}
