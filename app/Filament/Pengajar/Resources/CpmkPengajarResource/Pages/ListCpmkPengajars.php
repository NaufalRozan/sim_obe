<?php

namespace App\Filament\Pengajar\Resources\CpmkPengajarResource\Pages;

use App\Filament\Pengajar\Resources\CpmkPengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListCpmkPengajars extends ListRecords
{
    protected static string $resource = CpmkPengajarResource::class;

    public static ?string $title = 'CPMK Matakuliah';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        if ($user->role === 'Dosen' || $user->role === 'Staf') {
            // Untuk role Dosen atau Staf, hanya tampilkan data CPMK terkait pengajar tersebut
            return parent::getTableQuery()
                ->whereHas('pengajars', function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        } else {
            // Jika bukan Dosen atau Staf, tampilkan data sesuai dengan prodi user tersebut
            $kurikulumIds = $user->prodis->flatMap(function ($prodi) {
                return $prodi->kurikulums->pluck('id');
            })->toArray();

            return parent::getTableQuery()
                ->whereHas('mk', function (Builder $query) use ($kurikulumIds) {
                    $query->whereIn('kurikulum_id', $kurikulumIds);
                });
        }
    }
}
