<?php

namespace App\Filament\Resources\LaporanGrafikResource\Pages;

use App\Filament\Resources\LaporanGrafikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanGrafik extends EditRecord
{
    protected static string $resource = LaporanGrafikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
