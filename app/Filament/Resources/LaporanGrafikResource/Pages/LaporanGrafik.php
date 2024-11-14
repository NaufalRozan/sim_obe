<?php

namespace App\Filament\Resources\LaporanGrafikResource\Pages;

use App\Filament\Resources\LaporanGrafikResource;
use App\Filament\Resources\LaporanGrafikResource\Widgets\Filters;
use App\Filament\Resources\LaporanGrafikResource\Widgets\LaporanChart;
use Filament\Forms\Components\Builder;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class LaporanGrafik extends Page
{
    protected static string $resource = LaporanGrafikResource::class;

    protected static string $view = 'filament.resources.laporan-grafik-resource.pages.laporan-grafik';

    protected static ?string $title = 'Laporan Grafik';

    protected function getHeaderWidgets(): array
    {
        return [
            Filters::class,
            LaporanChart::class,
        ];
    }

    protected function updatedFilters($name, $value)
    {
        $this->emit('filterUpdated', $name, $value);
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Filter berdasarkan prodi user yang sedang login
        $user = Auth::user();
        if ($user && $user->prodis) {
            $prodiIds = $user->prodis->pluck('id')->toArray();

            $query->whereHas('mkDitawarkan.mk.kurikulum.prodi', function (Builder $query) use ($prodiIds) {
                $query->whereIn('id', $prodiIds);
            });
        }

        return $query;
    }
}
