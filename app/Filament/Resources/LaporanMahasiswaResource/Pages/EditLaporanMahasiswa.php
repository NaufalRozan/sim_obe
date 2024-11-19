<?php

namespace App\Filament\Resources\LaporanMahasiswaResource\Pages;

use App\Filament\Resources\LaporanMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanMahasiswa extends EditRecord
{
    protected static string $resource = LaporanMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
