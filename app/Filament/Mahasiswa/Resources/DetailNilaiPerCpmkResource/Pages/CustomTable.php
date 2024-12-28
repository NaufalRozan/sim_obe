<?php

namespace App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource\Pages;

use App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource;
use App\Models\User;
use Filament\Resources\Pages\Page;

class CustomTable extends Page
{
    protected static string $resource = DetailNilaiPerCpmkResource::class;

    protected static string $view = 'filament.mahasiswa.resources.detail-nilai-per-cpmk-resource.pages.custom-table';

    public static ?string $title = 'Nilai Per CPMK';

    public User $mahasiswa; // Deklarasi properti mahasiswa

    public function mount()
    {
        $this->mahasiswa = User::where('id', request()->query('mahasiswa_id'))
            ->with(['krsMahasiswas.cpmkMahasiswa.cpmk.cplMk.mk', 'prodis'])
            ->firstOrFail();
    }
}
