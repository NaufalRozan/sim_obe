<?php

namespace App\Filament\Pengajar\Resources\LaporanResource\Widgets;

use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Filters extends Widget implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.pengajar.resources.laporan-resource.widgets.filters';

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

                                // Tampilkan hanya MK Ditawarkan yang sesuai dengan tahun ajaran yang dipilih
                                if ($tahunAjaranId && $user && $user->pengajar) {
                                    return \App\Models\MkDitawarkan::whereHas('pengajars', function ($query) use ($user) {
                                        $query->where('pengajar_id', $user->pengajar->id);
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
