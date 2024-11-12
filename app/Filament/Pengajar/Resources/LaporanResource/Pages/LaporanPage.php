<?php

namespace App\Filament\Pengajar\Resources\LaporanResource\Pages;

use App\Filament\Pengajar\Resources\LaporanResource;
use App\Filament\Pengajar\Resources\LaporanResource\Widgets\Filters;
use App\Filament\Pengajar\Resources\LaporanResource\Widgets\LaporanChart;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanPage extends Page
{
    protected static string $resource = LaporanResource::class;

    protected static string $view = 'filament.pengajar.resources.laporan-resource.pages.laporan-page';

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
