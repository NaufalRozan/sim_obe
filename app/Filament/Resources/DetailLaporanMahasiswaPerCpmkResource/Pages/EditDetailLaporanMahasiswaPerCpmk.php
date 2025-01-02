<?php

namespace App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource\Pages;

use App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetailLaporanMahasiswaPerCpmk extends EditRecord
{
    protected static string $resource = DetailLaporanMahasiswaPerCpmkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
