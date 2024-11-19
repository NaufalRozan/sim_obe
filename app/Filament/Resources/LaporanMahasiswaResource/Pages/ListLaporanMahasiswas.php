<?php

namespace App\Filament\Resources\LaporanMahasiswaResource\Pages;

use App\Filament\Resources\LaporanMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListLaporanMahasiswas extends ListRecords
{
    protected static string $resource = LaporanMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
