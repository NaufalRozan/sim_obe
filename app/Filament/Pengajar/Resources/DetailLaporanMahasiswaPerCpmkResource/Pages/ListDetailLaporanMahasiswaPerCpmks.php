<?php

namespace App\Filament\Pengajar\Resources\DetailLaporanMahasiswaPerCpmkResource\Pages;

use App\Filament\Pengajar\Resources\DetailLaporanMahasiswaPerCpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ListDetailLaporanMahasiswaPerCpmks extends ListRecords
{
    protected static string $resource = DetailLaporanMahasiswaPerCpmkResource::class;

    public static ?string $title = 'Detail Laporan Mahasiswa per CPMK';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        $mahasiswaId = request()->query('mahasiswa_id'); // Ambil ID mahasiswa dari URL parameter

        // Jika parameter mahasiswa_id kosong, kembalikan query kosong
        if (!$mahasiswaId) {
            return User::query()->whereRaw('1 = 0'); // Query kosong (tidak akan menampilkan data)
        }

        // Jika parameter mahasiswa_id ada, filter berdasarkan ID tersebut
        return User::query()
            ->where('role', 'Mahasiswa') // Hanya mahasiswa
            ->where('id', $mahasiswaId); // Filter berdasarkan ID mahasiswa
    }
}
