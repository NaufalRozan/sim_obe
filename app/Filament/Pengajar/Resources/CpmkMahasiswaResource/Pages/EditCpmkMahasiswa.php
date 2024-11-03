<?php

namespace App\Filament\Pengajar\Resources\CpmkMahasiswaResource\Pages;

use App\Filament\Pengajar\Resources\CpmkMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpmkMahasiswa extends EditRecord
{
    protected static string $resource = CpmkMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
