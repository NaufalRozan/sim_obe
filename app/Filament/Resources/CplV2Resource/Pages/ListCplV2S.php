<?php

namespace App\Filament\Resources\CplV2Resource\Pages;

use App\Filament\Resources\CplV2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListCplV2S extends ListRecords
{
    protected static string $resource = CplV2Resource::class;

    protected static ?string $title = 'CPL Prodi V2';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        $kurikulumIds = $user->prodis->flatMap(function ($prodi) {
            return $prodi->kurikulums->pluck('id');
        })->toArray();

        return parent::getTableQuery()
            ->whereIn('kurikulum_id', $kurikulumIds)
            ->whereHas('kurikulum.prodi', function ($query) use ($user) {
                $query->whereIn('id', $user->prodis->pluck('id')->toArray());
            });
    }
}
