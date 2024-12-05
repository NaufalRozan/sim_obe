<?php

namespace App\Filament\Mahasiswa\Resources\LihatDetailMahasiswaResource\Pages;

use App\Filament\Mahasiswa\Resources\LihatDetailMahasiswaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListLihatDetailMahasiswas extends ListRecords
{
    protected static string $resource = LihatDetailMahasiswaResource::class;

    protected static ?string $title = 'Nilai CPMK';

    protected function getTableQuery(): Builder
    {
        $userId = Auth::id(); // Ambil ID user yang login

        // Batasi hanya data milik user yang login
        return parent::getTableQuery()->where('id', $userId);
    }

    protected function getActions(): array
    {
        return [];
    }
}
