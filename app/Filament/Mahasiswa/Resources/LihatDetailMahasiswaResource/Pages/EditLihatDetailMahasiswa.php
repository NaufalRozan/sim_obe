<?php

namespace App\Filament\Mahasiswa\Resources\LihatDetailMahasiswaResource\Pages;

use App\Filament\Mahasiswa\Resources\LihatDetailMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLihatDetailMahasiswa extends EditRecord
{
    protected static string $resource = LihatDetailMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
