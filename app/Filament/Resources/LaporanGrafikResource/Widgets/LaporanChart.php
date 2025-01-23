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

    protected static ?string $heading = 'Grafik Nilai CPMK';
    protected int | string | array $columnSpan = 'full';

    public ?int $mkDitawarkanId = null; // Properti untuk menyimpan ID MK Ditawarkan

    #[On('mkDitawarkanIdUpdate')]
    public function mkDitawarkanIdUpdate($mkDitawarkanId)
    {
        $this->mkDitawarkanId = $mkDitawarkanId; // Tetapkan ID MK Ditawarkan
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // Pastikan $mkDitawarkanId tersedia
        if (!$this->mkDitawarkanId) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Ambil data nilai CPMK dan batas nilai untuk masing-masing CPMK
        $mkDitawarkan = MkDitawarkan::find($this->mkDitawarkanId);

        if (!$mkDitawarkan) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Ambil data nilai rata-rata per CPMK
        $data = CpmkMahasiswa::whereHas('krsMahasiswa.mkDitawarkan', function ($query) {
            $query->where('id', $this->mkDitawarkanId);
        })
            ->join('cpmk', 'cpmk_mahasiswa.cpmk_id', '=', 'cpmk.id')
            ->select('cpmk.kode_cpmk', 'cpmk_mahasiswa.nilai', 'cpmk.batas_nilai_lulus', 'cpmk.batas_nilai_memuaskan')
            ->get()
            ->groupBy('kode_cpmk')
            ->map(function ($group) {
                return [
                    'avg_nilai' => $group->avg('nilai'),
                    'batas_nilai_lulus' => $group->first()->batas_nilai_lulus,
                    'batas_nilai_memuaskan' => $group->first()->batas_nilai_memuaskan,
                ];
            });

        // Siapkan dataset untuk grafik
        $labels = $data->keys()->toArray();
        $avgNilai = $data->pluck('avg_nilai')->toArray();
        $batasNilaiLulus = $data->pluck('batas_nilai_lulus')->toArray();
        $batasNilaiMemuaskan = $data->pluck('batas_nilai_memuaskan')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nilai Rata-Rata CPMK',
                    'data' => $avgNilai,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Batas Nilai Lulus',
                    'data' => $batasNilaiLulus,
                    'type' => 'line',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Batas Nilai Memuaskan',
                    'data' => $batasNilaiMemuaskan,
                    'type' => 'line',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'pointRadius' => 0,
                ],
            ],
        ];
    }
}
