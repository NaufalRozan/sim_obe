<?php

namespace App\Filament\Pengajar\Resources\LaporanMahasiswaResource\Pages;

use App\Filament\Pengajar\Resources\LaporanMahasiswaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListLaporanMahasiswas extends ListRecords
{
    protected static string $resource = LaporanMahasiswaResource::class;

    //title
    public static ?string $title = 'Laporan Mahasiswa per CPMK';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();

        // Validasi jika user belum login
        if (!$user) {
            abort(403, 'Anda harus login untuk mengakses data ini.');
        }

        // Validasi jika user tidak memiliki relasi prodi
        if ($user->prodis->isEmpty()) {
            abort(403, 'Anda tidak memiliki akses ke prodi tertentu.');
        }

        return User::query()
            ->where('role', 'Mahasiswa') // Filter hanya mahasiswa
            ->whereHas('prodis', function (Builder $query) use ($user) {
                $query->whereIn('prodi_id', $user->prodis->pluck('id'));
            })
            ->whereHas('pengajars', function (Builder $query) use ($user) {
                $query->where('pengajar_id', $user->id);
            });
    }
}
