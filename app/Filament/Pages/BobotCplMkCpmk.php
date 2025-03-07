<?php

namespace App\Filament\Pages;

use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Mk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class BobotCplMkCpmk extends Page
{
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Pemetaan';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Bobot CPL - MK - CPMK';

    protected static string $view = 'filament.pages.bobot-cpl-mk-cpmk';

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
