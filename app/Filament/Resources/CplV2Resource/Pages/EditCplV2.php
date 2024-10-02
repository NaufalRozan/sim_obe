<?php

namespace App\Filament\Resources\CplV2Resource\Pages;

use App\Filament\Resources\CplV2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCplV2 extends EditRecord
{
    protected static string $resource = CplV2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
