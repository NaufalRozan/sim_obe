<?php

namespace App\Filament\Resources\LaporanRekapTindakLanjutResource\Pages;

use App\Filament\Resources\LaporanRekapTindakLanjutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanRekapTindakLanjut extends EditRecord
{
    protected static string $resource = LaporanRekapTindakLanjutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
