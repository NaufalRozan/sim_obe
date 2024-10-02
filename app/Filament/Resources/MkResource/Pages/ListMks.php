<?php

namespace App\Filament\Resources\MkResource\Pages;

use App\Filament\Resources\MkResource;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListMks extends ListRecords
{
    protected static string $resource = MkResource::class;

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
        $kurikulumIds = $user->prodis->flatMap(function ($prodi) {
            return $prodi->kurikulums->pluck('id');
        })->toArray();

        // Hanya menampilkan data cpl sesuai dengan kurikulum user
        return parent::getTableQuery()->whereIn('kurikulum_id', $kurikulumIds);
    }
}
