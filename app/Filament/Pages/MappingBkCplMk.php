<?php

namespace App\Filament\Pages;

use App\Models\BahanKajian;
use App\Models\Cpl;
use App\Models\Mk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MappingBkCplMk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pemetaan BK - CPL - MK';

    protected static string $view = 'filament.pages.mapping-bk-cpl-mk';

    public $bks;
    public $cpls;
    public $mks;

    public function mount()
    {
        $user = Auth::user();

        // Ambil prodi ID dari user yang login
        $prodiIds = $user->prodis->pluck('id')->toArray();

        $this->bks = BahanKajian::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('mks')->get();

        // Ambil data CPL dan MK dengan kurikulum yang sesuai
        $this->cpls = Cpl::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('mks')->get();

        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('cpls')->get();
    }
}
