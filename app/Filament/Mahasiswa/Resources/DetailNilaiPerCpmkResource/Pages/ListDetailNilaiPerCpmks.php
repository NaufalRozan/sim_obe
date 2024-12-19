<?php

namespace App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource\Pages;

use App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDetailNilaiPerCpmks extends ListRecords
{
    protected static string $resource = DetailNilaiPerCpmkResource::class;

    public static ?string $title = 'Detail Nilai per CPMK';

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
