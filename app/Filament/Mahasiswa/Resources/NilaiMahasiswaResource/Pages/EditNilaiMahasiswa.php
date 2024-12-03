<?php

namespace App\Filament\Mahasiswa\Resources\NilaiMahasiswaResource\Pages;

use App\Filament\Mahasiswa\Resources\NilaiMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiMahasiswa extends EditRecord
{
    protected static string $resource = NilaiMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
