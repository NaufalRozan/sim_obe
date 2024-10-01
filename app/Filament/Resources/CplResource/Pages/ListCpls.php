<?php

namespace App\Filament\Resources\CplResource\Pages;

use App\Filament\Resources\CplResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ListCpls extends ListRecords
{
    protected static string $resource = CplResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    //hanya menampilkan cpl yang berelasi dengan kurikulum prodi user
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
