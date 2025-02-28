<?php

namespace App\Filament\Pages;

use App\Models\Cpl;
use App\Models\Mk;
use App\Models\Cpmk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MappingCplCpmkMk extends Page
{
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Pemetaan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pemetaan CPL - CPMK - MK';

    protected static string $view = 'filament.pages.mapping-cpl-cpmk-mk';

    public $cpls;
    public $mks;
    public $cpmks;

    public function mount()
    {
        $user = Auth::user();

        // Ambil prodi ID dari user yang login
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil data CPL dengan MK dan CPMK yang sesuai dengan kurikulum pengguna
        $this->cpls = Cpl::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['mks.cpmks'])->get();

        // Ambil data MK yang sesuai dengan kurikulum pengguna
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('cpls', 'cpmks')->get();

        // Ambil semua CPMK yang terkait dengan MK
        $this->cpmks = Cpmk::whereHas('mk', function ($query) use ($prodiIds) {
            $query->whereHas('kurikulum', function ($q) use ($prodiIds) {
                $q->whereIn('prodi_id', $prodiIds);
            });
        })->get();
    }
}
