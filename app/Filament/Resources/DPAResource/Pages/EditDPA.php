<?php

namespace App\Filament\Resources\DPAResource\Pages;

use App\Filament\Resources\DPAResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDPA extends EditRecord
{
    protected static string $resource = DPAResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
