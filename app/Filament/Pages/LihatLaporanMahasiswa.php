<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class LihatLaporanMahasiswa extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.lihat-laporan-mahasiswa';
    protected static bool $shouldRegisterNavigation = false;

    public $krs_mahasiswa_id;
    public $mk_ditawarkan_id;

    public function mount($krs_mahasiswa_id, $mk_ditawarkan_id)
    {
        $this->krs_mahasiswa_id = $krs_mahasiswa_id;
        $this->mk_ditawarkan_id = $mk_ditawarkan_id;
    }
}
