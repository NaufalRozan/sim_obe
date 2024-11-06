<?php

namespace App\Filament\Pengajar\Resources\LaporanResource\Pages;

use App\Filament\Pengajar\Resources\LaporanResource;
use App\Filament\Pengajar\Resources\LaporanResource\Widgets\LaporanChart;
use Filament\Resources\Pages\ListRecords;

class LaporanPage extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            LaporanChart::class,
        ];
    }

    protected function updatedFilters($name, $value)
    {
        // Emit the event to update mk_ditawarkan_id in LaporanChart
        if ($name === 'filter_tahun_ajaran_mk.mk_ditawarkan_id') {
            $this->emit('updateMkDitawarkanId', $value);
        }
    }
}
