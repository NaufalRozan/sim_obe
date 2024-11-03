<?php

namespace App\Imports;

use App\Models\Cpmk;
use App\Models\CpmkMahasiswa;
use App\Models\KrsMahasiswa;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CpmkMahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari user berdasarkan NIM
        $user = User::where('nim', $row['nim'])->first();

        // Pastikan user dan KRS Mahasiswa terkait ada
        if ($user) {
            $krsMahasiswa = KrsMahasiswa::where('user_id', $user->id)->first();

            if ($krsMahasiswa) {
                // Looping melalui semua CPMK yang sesuai untuk MK yang ditawarkan
                $cpmkIds = Cpmk::whereHas('mkDitawarkan', function ($query) use ($krsMahasiswa) {
                    $query->where('id', $krsMahasiswa->mk_ditawarkan_id);
                })->pluck('id');

                foreach ($cpmkIds as $cpmkId) {
                    if (isset($row["nilai_cpmk_$cpmkId"])) {
                        CpmkMahasiswa::create([
                            'cpmk_id' => $cpmkId,
                            'krs_mahasiswa_id' => $krsMahasiswa->id,
                            'nilai' => $row["nilai_cpmk_$cpmkId"],
                        ]);
                    }
                }
            }
        }
    }
}
