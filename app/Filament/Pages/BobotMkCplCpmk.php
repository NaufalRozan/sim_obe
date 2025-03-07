<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Cpl;
use App\Models\Mk;
use Illuminate\Support\Facades\Auth;

class BobotMkCplCpmk extends Page
{
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationGroup = 'Pemetaan';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Bobot MK - CPL - CPMK';

    protected static string $view = 'filament.pages.bobot-mk-cpl-cpmk';

    public $mks;
    public $cpls;

    public function mount()
    {
        $user = Auth::user();

        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil data MK yang memiliki CPL dan CPMK
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['cpls', 'cpmks'])->get();

        // Ambil CPL yang terkait dengan MK
        $this->cpls = Cpl::whereHas('mks.kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->get();
    }
}
