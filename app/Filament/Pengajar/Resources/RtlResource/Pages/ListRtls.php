<?php

namespace App\Filament\Pengajar\Resources\RtlResource\Pages;

use App\Filament\Pengajar\Resources\RtlResource;
use App\Models\CpmkMahasiswa;
use App\Models\MkDitawarkan;
use App\Models\TahunAjaran;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ListRtls extends ListRecords
{
    protected static string $resource = RtlResource::class;

    protected static ?string $title = 'Tindak Lanjut';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->form([
                    Select::make('tahun_ajaran_id')
                        ->label('Tahun Ajaran')
                        ->options(TahunAjaran::pluck('nama_tahun_ajaran', 'id'))
                        ->required()
                        ->reactive()
                        ->placeholder('Pilih Tahun Ajaran'),

                    Select::make('mk_ditawarkan_id')
                        ->label('MK Ditawarkan')
                        ->options(function (callable $get) {
                            $tahunAjaranId = $get('tahun_ajaran_id');
                            $user = Auth::user();

                            if (!$tahunAjaranId || !$user) {
                                return [];
                            }

                            return MkDitawarkan::whereHas('pengajars', function ($query) use ($user) {
                                $query->where('pengajar_id', $user->pengajar->id);
                            })
                                ->whereHas('semester', function ($query) use ($tahunAjaranId) {
                                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                                })
                                ->with('mk')
                                ->get()
                                ->unique('mk_id')
                                ->mapWithKeys(function ($mkDitawarkan) {
                                    return [$mkDitawarkan->id => $mkDitawarkan->mk->nama_mk ?? ''];
                                });
                        })
                        ->required()
                        ->searchable()
                        ->placeholder('Pilih MK Ditawarkan')
                        ->disabled(fn(callable $get) => !$get('tahun_ajaran_id')),
                ])
                ->action(function (array $data) {
                    return $this->exportPdf($data['mk_ditawarkan_id']);
                })
                ->requiresConfirmation('Apakah Anda yakin ingin melakukan export PDF untuk MK Ditawarkan yang dipilih?'),
        ];
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

    public function exportPdf(int $mkDitawarkanId)
    {
        $mkDitawarkan = MkDitawarkan::with(['mk', 'cpmks.cplMk.cpl'])->findOrFail($mkDitawarkanId);

        $pdfData = [
            'mkName' => $mkDitawarkan->mk->nama_mk,
            'cpls' => $mkDitawarkan->cpmks->groupBy('cplMk.cpl.nama_cpl')
                ->map(function ($cpmks) {
                    return [
                        'nama_cpl' => $cpmks->first()->cplMk->cpl->nama_cpl,
                        'cpmks' => $cpmks->mapWithKeys(function ($cpmk) {
                            return [$cpmk->kode_cpmk => $cpmk->deskripsi];
                        })->toArray(),
                    ];
                })->toArray(),
        ];

        // Dapatkan data grafik
        $chartData = $this->getChartData($mkDitawarkanId);

        // Membuat URL QuickChart secara manual
        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $chartData['labels'],
                'datasets' => [[
                    'label' => 'Nilai Rata-Rata CPMK',
                    'data' => $chartData['data'],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ]]
            ],
            'options' => [
                'scales' => [
                    'y' => ['beginAtZero' => true]
                ]
            ]
        ]));

        // Menambahkan URL gambar ke data yang dikirim ke PDF
        $pdfData['chartUrl'] = $chartUrl;

        // Ambil data laporan terkait dengan MK Ditawarkan
        $pdfData['reports'] = \App\Models\Laporan::where('mk_ditawarkan_id', $mkDitawarkanId)->get();

        // Generate PDF dan download file
        $pdf = PDF::loadView('exports.mk_ditawarkan_pdf', $pdfData);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Export_MK_{$mkDitawarkan->mk->nama_mk}.pdf");
    }


    protected function getChartData($mkDitawarkanId)
    {
        $mkId = MkDitawarkan::where('id', $mkDitawarkanId)->pluck('mk_id')->first();

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
            'data' => $data->values()->toArray(),
        ];
    }
}
