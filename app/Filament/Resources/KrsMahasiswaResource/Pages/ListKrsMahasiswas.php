<?php

namespace App\Filament\Resources\KrsMahasiswaResource\Pages;

use App\Filament\Resources\KrsMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListKrsMahasiswas extends ListRecords
{
    protected static string $resource = KrsMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Mendapatkan prodi_id dari user yang login
        $user = Auth::user();
        $prodiIds = $user->prodis->pluck('id')->toArray(); // Prodi terkait user

        // Membatasi data hanya untuk mahasiswa terkait prodi user yang login
        return parent::getTableQuery()
            ->whereHas('user.prodis', function (Builder $query) use ($prodiIds) {
                $query->whereIn('prodis.id', $prodiIds);
            });
    }
}
