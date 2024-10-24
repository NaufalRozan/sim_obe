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

    public static ?string $title = 'CPMK Matakuliah';

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

        // Query dengan join ke tabel mk untuk membatasi berdasarkan kurikulum
        return parent::getTableQuery()
            ->whereHas('mk', function (Builder $query) use ($kurikulumIds) {
                $query->whereIn('kurikulum_id', $kurikulumIds);
            });
    }
}
