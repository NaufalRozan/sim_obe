<?php

namespace App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource\Pages;

use App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource;
use App\Models\User;
use Filament\Resources\Pages\Page;

class CustomTable extends Page
{
    protected static string $resource = DetailLaporanMahasiswaPerCpmkResource::class;

    protected static string $view = 'filament.resources.detail-laporan-mahasiswa-per-cpmk-resource.pages.custom-table';

    public static ?string $title = 'Detail Laporan Mahasiswa per CPMK';

    public User $mahasiswa; // Deklarasi properti mahasiswa

    public function mount()
    {
        $this->mahasiswa = User::where('id', request()->query('mahasiswa_id'))
            ->with(['krsMahasiswas.cpmkMahasiswa.cpmk.cplMk.mk', 'prodis'])
            ->firstOrFail();
    }
}
