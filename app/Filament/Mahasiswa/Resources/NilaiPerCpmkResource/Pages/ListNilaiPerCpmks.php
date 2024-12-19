<?php

namespace App\Filament\Mahasiswa\Resources\NilaiPerCpmkResource\Pages;

use App\Filament\Mahasiswa\Resources\NilaiPerCpmkResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ListNilaiPerCpmks extends ListRecords
{
    protected static string $resource = NilaiPerCpmkResource::class;

    protected static ?string $title = 'Nilai per CPMK';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        // Ambil ID mahasiswa yang sedang login
        $mahasiswaId = Auth::id();

        return User::query()
            ->where('id', $mahasiswaId); // Filter hanya data mahasiswa yang login
    }
}
