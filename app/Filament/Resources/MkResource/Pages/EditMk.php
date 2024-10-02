<?php

namespace App\Filament\Resources\MkResource\Pages;

use App\Filament\Resources\MkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMk extends EditRecord
{
    protected static string $resource = MkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
