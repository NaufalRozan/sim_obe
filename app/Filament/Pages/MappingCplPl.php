<?php

namespace App\Filament\Pages;

use App\Models\Cpl;
use App\Models\Pl;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MappingCplPl extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Pemetaan CPL dan PL';

    protected static string $view = 'filament.pages.mapping-cpl-pl';

    public $cpls;
    public $pls;

    public function mount()
    {
        $user = Auth::user();

        // Ambil prodi ID dari user yang login
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil data CPL dan PL dengan kurikulum yang sesuai
        $this->cpls = Cpl::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('pls')->get();

        $this->pls = Pl::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with('cpls')->get();
    }
}
