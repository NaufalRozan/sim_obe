<?php

namespace App\Filament\Pages;

use App\Models\Cpl;
use App\Models\Mk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MappingCplMkCpmk extends Page
{
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationGroup = 'Pemetaan';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pemetaan CPL - MK - CPMK';

    protected static string $view = 'filament.pages.mapping-cpl-mk-cpmk';

    public $mks;
    public $cpls;

    public function mount()
    {
        $user = Auth::user();

        // Ambil prodi ID dari user yang login
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil data MK dengan relasi CPL dan CPMK
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['cpls', 'cpmks'])->get();

        // Ambil data CPL yang terkait dengan MK, lalu urutkan berdasarkan nomor CPL
        $this->cpls = Cpl::whereHas('mks.kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->orderByRaw("CAST(REGEXP_SUBSTR(nama_cpl, '[0-9]+') AS UNSIGNED) ASC")->get();
    }
}
