<?php

namespace App\Filament\Resources\CpmkResource\Pages;

use App\Filament\Resources\CpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpmk extends EditRecord
{
    protected static string $resource = CpmkResource::class;

    public static ?string $title = 'Matakuliah';

    protected static ?string $breadcrumb = 'Matakuliah';


    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
