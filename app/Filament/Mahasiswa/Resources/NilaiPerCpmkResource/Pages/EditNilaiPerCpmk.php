<?php

namespace App\Filament\Mahasiswa\Resources\NilaiPerCpmkResource\Pages;

use App\Filament\Mahasiswa\Resources\NilaiPerCpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiPerCpmk extends EditRecord
{
    protected static string $resource = NilaiPerCpmkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
