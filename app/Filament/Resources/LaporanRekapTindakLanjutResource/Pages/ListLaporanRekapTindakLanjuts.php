<?php

namespace App\Filament\Resources\LaporanRekapTindakLanjutResource\Pages;

use App\Filament\Resources\LaporanRekapTindakLanjutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListLaporanRekapTindakLanjuts extends ListRecords
{
    protected static string $resource = LaporanRekapTindakLanjutResource::class;

    public static ?string $title = 'Laporan Rekap Tindak Lanjut';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    //hanya menampilkan yang sesuai prodi
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Filter berdasarkan prodi user yang sedang login
        $user = Auth::user();
        if ($user && $user->prodis) {
            $prodiIds = $user->prodis->pluck('id')->toArray();

            $query->whereHas('mkDitawarkan.mk.kurikulum.prodi', function (Builder $query) use ($prodiIds) {
                $query->whereIn('id', $prodiIds);
            });
        }

        return $query;
    }
}
