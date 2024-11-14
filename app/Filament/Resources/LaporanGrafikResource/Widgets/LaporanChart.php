<?php

namespace App\Filament\Resources\LaporanGrafikResource\Widgets;

use App\Models\CpmkMahasiswa;
use App\Models\MkDitawarkan;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Attributes\On;

class LaporanChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Nilai CPMK';
    protected int | string | array $columnSpan = 'full';

    // Mengatur nilai awal untuk menghindari error
    public ?int $mk_ditawarkan_id = null;

    protected function getType(): string
    {
        return 'bar';
    }

    #[On('mkDitawarkanIdUpdate')]
    public function mkDitawarkanIdUpdate($mkDitawarkanId)
    {
        $this->mk_ditawarkan_id = $mkDitawarkanId;
    }

    protected function getData(): array
    {
        // Memastikan bahwa $mk_ditawarkan_id tidak null sebelum digunakan
        if ($this->mk_ditawarkan_id === null) {
            return [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Nilai Rata-Rata CPMK',
                        'data' => [],
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'borderWidth' => 1,
                    ],
                ],
            ];
        }

        $mkId = MkDitawarkan::where('id', $this->mk_ditawarkan_id)
            ->pluck('mk_id')
            ->first();

        $data = CpmkMahasiswa::whereHas('krsMahasiswa.mkDitawarkan', function ($query) use ($mkId) {
            $query->where('mk_id', $mkId);
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
