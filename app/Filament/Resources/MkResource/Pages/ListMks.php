<?php

namespace App\Filament\Resources\MkResource\Pages;

use App\Filament\Resources\MkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMks extends ListRecords
{
    protected static string $resource = MkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
