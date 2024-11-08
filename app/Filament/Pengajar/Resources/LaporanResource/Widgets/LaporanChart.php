<?php

namespace App\Filament\Pengajar\Resources\LaporanResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CpmkMahasiswa;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class LaporanChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Nilai CPMK';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'today';

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
        // Ambil ID MK dari mk_ditawarkan_id yang dipilih
        $mkId = \App\Models\MkDitawarkan::where('id', $this->mk_ditawarkan_id)
            ->pluck('mk_id')
            ->first();

        // Ambil data nilai CPMK dari semua kelas yang terkait dengan MK yang dipilih
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
