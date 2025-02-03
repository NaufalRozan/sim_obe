<?php

namespace App\Filament\Pages;

use App\Models\BahanKajian;
use App\Models\Cpl;
use App\Models\Mk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MappingBkCpl extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pemetaan BK dan CPL';

    protected static string $view = 'filament.pages.mapping-bk-cpl';

    public $bks;
    public $cpls;
    public $mks;

    public function mount()
    {
        $user = Auth::user();

        // Ambil prodi ID dari user yang login
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil data BK yang terkait dengan kurikulum prodi user
        $this->bks = BahanKajian::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('mks')->get();

        // Ambil data CPL yang terkait dengan kurikulum prodi user
        $this->cpls = Cpl::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->get();

        // Ambil semua MK yang terkait dengan BK dan CPL
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['bks', 'cpls'])->get();
    }
}
