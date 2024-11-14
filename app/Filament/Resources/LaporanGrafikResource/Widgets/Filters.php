<?php

namespace App\Filament\Resources\LaporanGrafikResource\Widgets;

use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Filters extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.laporan-grafik-resource.widgets.filters';
    protected array|string|int $columnSpan = 'full';
    protected static ?int $sort = 1;
    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                // Filter mk_ditawarkan_id
                Grid::make(2)
                    ->schema([
                        // Tahun Ajaran
                        Select::make('tahun_ajaran_id')
                            ->label('Tahun Ajaran')
                            ->placeholder('Pilih Tahun Ajaran')
                            ->options(function () {
                                return \App\Models\TahunAjaran::pluck('nama_tahun_ajaran', 'id')->toArray();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Reset pilihan MK Ditawarkan ketika Tahun Ajaran berubah
                                $set('mk_ditawarkan_id', null);
                            }),

                        // MK Ditawarkan
                        Select::make('mk_ditawarkan_id')
                            ->statePath('data')
                            ->label('MK Ditawarkan')
                            ->placeholder('Pilih MK Ditawarkan')
                            ->options(function (callable $get) {
                                $tahunAjaranId = $get('tahun_ajaran_id');
                                $user = Auth::user();

                                // Filter MK Ditawarkan berdasarkan Tahun Ajaran dan Prodi user yang sedang login
                                if ($tahunAjaranId && $user) {
                                    // Dapatkan ID prodi yang terkait dengan user
                                    $prodiIds = $user->prodis->pluck('id')->toArray();

                                    return \App\Models\MkDitawarkan::whereHas('mk.kurikulum.prodi', function ($query) use ($prodiIds) {
                                        $query->whereIn('id', $prodiIds);
                                    })
                                        ->whereHas('semester', function ($query) use ($tahunAjaranId) {
                                            $query->where('tahun_ajaran_id', $tahunAjaranId);
                                        })
                                        ->with('mk') // Load relasi MK agar bisa menampilkan nama MK
                                        ->get()
                                        ->groupBy('mk.nama_mk') // Mengelompokkan berdasarkan nama MK
                                        ->mapWithKeys(function ($groupedMkDitawarkan) {
                                            // Mengambil satu instance MK Ditawarkan per nama MK
                                            $firstMkDitawarkan = $groupedMkDitawarkan->first();
                                            return [$firstMkDitawarkan->id => $firstMkDitawarkan->mk->nama_mk];
                                        });
                                }

                                return [];
                            })
                            ->disabled(fn(callable $get) => !$get('tahun_ajaran_id')) // Nonaktifkan jika tahun ajaran belum dipilih
                            ->reactive()
                            ->live()
                            ->afterStateUpdated(fn(?int $state) => $this->dispatch('mkDitawarkanIdUpdate', $state))
                            ->preload(),
                    ]),
            ]);
    }
}
