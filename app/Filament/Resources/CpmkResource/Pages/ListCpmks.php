<?php

namespace App\Filament\Resources\CpmkResource\Pages;

use App\Filament\Resources\CpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListCpmks extends ListRecords
{
    protected static string $resource = CpmkResource::class;

    public static ?string $title = 'CPMK';

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
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Hanya menampilkan data kurikulum sesuai dengan prodi user
        return parent::getTableQuery()->whereIn('prodi_id', $prodiIds);
    }
}
