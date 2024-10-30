<?php

namespace App\Filament\Resources\PengajarResource\Pages;

use App\Filament\Resources\PengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPengajars extends ListRecords
{
    protected static string $resource = PengajarResource::class;

    public static ?string $title = 'Pengajar';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user(); // Mendapatkan user yang sedang login
        $prodiIds = $user->prodis->pluck('id'); // Mendapatkan prodi terkait user

        // Filter hanya pengajar yang terkait dengan prodi user
        return parent::getTableQuery()->whereHas('user.prodis', function (Builder $query) use ($prodiIds) {
            $query->whereIn('prodis.id', $prodiIds);
        });
    }
}
