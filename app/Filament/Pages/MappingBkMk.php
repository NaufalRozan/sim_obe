<?php

namespace App\Filament\Pages;

use App\Models\BahanKajian;
use App\Models\Mk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MappingBkMk extends Page
{
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Pemetaan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pemetaan BK dan MK';
    protected static string $view = 'filament.pages.mapping-bk-mk';

    public $bks;
    public $mks;

    public function mount()
    {
        $user = Auth::user();

        // Ambil prodi ID dari user yang login
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Filter data BK berdasarkan kurikulum prodi
        $this->bks = BahanKajian::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('mks')->get();

        // Filter data MK berdasarkan kurikulum prodi
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->get();
    }
}
