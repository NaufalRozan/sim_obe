<?php

namespace App\Filament\Pengajar\Resources\LaporanResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CpmkMahasiswa;
use Illuminate\Support\Facades\DB;

class LaporanChart extends ChartWidget
{
    protected static ?string $heading = 'Nilai CPMK';

    public ?int $mk_ditawarkan_id = 2; // Default mk_ditawarkan_id

    protected $listeners = ['updateMkDitawarkanId' => 'setMkDitawarkanId'];

    public function setMkDitawarkanId($mk_ditawarkan_id)
    {
        $this->mk_ditawarkan_id = $mk_ditawarkan_id;
        $this->refresh(); // Refresh chart data
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $mkDitawarkanId = $this->mk_ditawarkan_id;

        $data = CpmkMahasiswa::whereHas('krsMahasiswa', function ($query) use ($mkDitawarkanId) {
            $query->where('mk_ditawarkan_id', $mkDitawarkanId);
        })
            ->join('cpmk', 'cpmk_mahasiswa.cpmk_id', '=', 'cpmk.id')
            ->select('cpmk.kode_cpmk', 'cpmk_mahasiswa.nilai')
            ->get()
            ->groupBy('kode_cpmk')
            ->map(function ($group) {
                return $group->avg('nilai');
            });

        return [
            'labels' => $data->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Nilai Rata-Rata CPMK',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
