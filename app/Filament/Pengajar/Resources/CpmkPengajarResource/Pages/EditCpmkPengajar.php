<?php

namespace App\Filament\Pengajar\Resources\CpmkPengajarResource\Pages;

use App\Filament\Pengajar\Resources\CpmkPengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpmkPengajar extends EditRecord
{
    protected static string $resource = CpmkPengajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
