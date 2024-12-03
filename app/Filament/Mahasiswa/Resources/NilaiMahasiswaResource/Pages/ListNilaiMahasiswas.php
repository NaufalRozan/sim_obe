<?php

namespace App\Filament\Mahasiswa\Resources\NilaiMahasiswaResource\Pages;

use App\Filament\Mahasiswa\Resources\NilaiMahasiswaResource;
use App\Models\Mk;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListNilaiMahasiswas extends ListRecords
{
    protected static string $resource = NilaiMahasiswaResource::class;

    protected static ?string $title = 'Nilai';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user(); // Mahasiswa yang login

        return Mk::query()
            ->whereHas('mkditawarkan.krsMahasiswas', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id); // Hanya MK yang sudah diambil oleh mahasiswa
            });
    }
}
