<?php

namespace App\Filament\Pengajar\Resources\RtlResource\Pages;

use App\Filament\Pengajar\Resources\RtlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRtl extends EditRecord
{
    protected static string $resource = RtlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
