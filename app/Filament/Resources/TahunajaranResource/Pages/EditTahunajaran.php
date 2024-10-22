<?php

namespace App\Filament\Resources\TahunajaranResource\Pages;

use App\Filament\Resources\TahunajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTahunajaran extends EditRecord
{
    protected static string $resource = TahunajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
