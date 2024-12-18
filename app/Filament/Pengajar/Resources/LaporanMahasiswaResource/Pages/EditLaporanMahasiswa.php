<?php

namespace App\Filament\Pengajar\Resources\LaporanMahasiswaResource\Pages;

use App\Filament\Pengajar\Resources\LaporanMahasiswaResource;
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
