<?php

namespace App\Imports;

use App\Models\Cpmk;
use App\Models\CpmkMahasiswa;
use App\Models\KrsMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CpmkMahasiswaImport implements ToModel, WithHeadingRow
{
    protected $cpmks;

    public function __construct()
    {
        $mkDitawarkanId = request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id');

        // Load all CPMKs that belong to the selected mk_ditawarkan_id
        $this->cpmks = Cpmk::whereHas('cplMk.mkDitawarkan', function ($query) use ($mkDitawarkanId) {
            $query->where('mk_ditawarkan.id', $mkDitawarkanId);
        })->get()->keyBy('kode_cpmk');

        // Log untuk melihat isi pemetaan
        Log::info('Pemetaan CPMKs', $this->cpmks->toArray());
    }

    public function model(array $row)
    {
        Log::info('Row yang diimpor', $row);

        // Find the user by NIM
        $user = User::where('nim', $row['nim'])->first();

        if (!$user) {
            Log::warning('User tidak ditemukan', ['NIM' => $row['nim']]);
            return null; // Skip if user not found
        }

        // Find the KRS entry for the user related to the MK Ditawarkan
        $krsMahasiswa = KrsMahasiswa::where('user_id', $user->id)
            ->where('mk_ditawarkan_id', request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id'))
            ->first();

        if (!$krsMahasiswa) {
            Log::warning('KRS Mahasiswa tidak ditemukan', ['User ID' => $user->id]);
            return null; // Skip if KRS not found
        }

        // Loop through each CPMK score in the row
        foreach ($row as $key => $value) {
            // Skip 'nama_mahasiswa' and 'nim' columns
            if (in_array($key, ['nama_mahasiswa', 'nim']) || $value === null) {
                continue;
            }

            // Convert Excel column format (cpmk_1) to match CPMK code (CPMK - 1)
            $kodeCpmk = strtoupper(str_replace('_', ' ', $key)); // Mengubah 'cpmk_1' menjadi 'CPMK 1'
            $kodeCpmk = str_replace('CPMK ', 'CPMK - ', $kodeCpmk); // Mengubah 'CPMK 1' menjadi 'CPMK - 1'

            // Tambahkan log untuk memantau kolom 'cpmk_1' secara khusus
            if ($key === 'cpmk_1') {
                Log::info('Proses CPMK 1', ['Key' => $key, 'Mapped Code' => $kodeCpmk, 'Value' => $value]);
            }

            // Find the corresponding CPMK by its code and mk_ditawarkan_id
            $cpmk = $this->cpmks->get($kodeCpmk);

            if ($cpmk) {
                // Update or create the CPMK Mahasiswa entry
                $result = CpmkMahasiswa::updateOrCreate(
                    [
                        'cpmk_id' => $cpmk->id,
                        'krs_mahasiswa_id' => $krsMahasiswa->id,
                    ],
                    [
                        'nilai' => $value,
                    ]
                );

                // Log hasil penyimpanan data
                Log::info('Hasil UpdateOrCreate', [
                    'CPMK ID' => $cpmk->id,
                    'KRS Mahasiswa ID' => $krsMahasiswa->id,
                    'Nilai' => $value,
                    'Result' => $result->wasRecentlyCreated ? 'Created' : 'Updated',
                ]);
            } else {
                Log::warning('CPMK tidak ditemukan untuk kolom', ['Kolom' => $key, 'Kode CPMK Pemetaan' => $kodeCpmk]);
            }
        }

        // Return null because no new model instance is needed
        return null;
    }
}
