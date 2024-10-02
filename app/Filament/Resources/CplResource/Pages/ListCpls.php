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

    protected function getTableQuery(): Builder
    {
        // Mendapatkan prodi_id dari user yang login
        $user = Auth::user();
        $kurikulumIds = $user->prodis->flatMap(function ($prodi) {
            return $prodi->kurikulums->pluck('id');
        })->toArray();

        // Hanya menampilkan data cpl sesuai dengan kurikulum user
        return parent::getTableQuery()
            ->whereIn('kurikulum_id', $kurikulumIds)
            ->whereHas('kurikulum.prodi', function ($query) use ($user) {
                $query->whereIn('id', $user->prodis->pluck('id')->toArray());
            });
    }
}
