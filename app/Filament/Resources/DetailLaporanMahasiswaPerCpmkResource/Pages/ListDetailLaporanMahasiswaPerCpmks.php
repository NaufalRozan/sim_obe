<?php

namespace App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource\Pages;

use App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailLaporanMahasiswaPerCpmks extends ListRecords
{
    protected static string $resource = DetailLaporanMahasiswaPerCpmkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
