<?php

namespace App\Filament\Resources\PlResource\Pages;

use App\Filament\Resources\PlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPl extends EditRecord
{
    protected static string $resource = PlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
