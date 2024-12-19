<?php

namespace App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource\Pages;

use App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetailNilaiPerCpmk extends EditRecord
{
    protected static string $resource = DetailNilaiPerCpmkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
