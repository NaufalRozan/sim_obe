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
                Select::make('mk_ditawarkan_id')
                    ->label('MK Ditawarkan')
                    ->live()
                    ->placeholder('Pilih MK Ditawarkan')
                    ->options(function () {
                        $user = Auth::user();

                        // Ambil semua MK Ditawarkan yang terkait dengan pengajar yang sedang login
                        return \App\Models\MkDitawarkan::whereHas('pengajars', function ($query) use ($user) {
                            $query->where('pengajar_id', $user->pengajar->id);
                        })
                            ->with('mk')
                            ->get()
                            ->unique('mk_id')
                            ->mapWithKeys(function ($mkDitawarkan) {
                                $namaMk = $mkDitawarkan->mk->nama_mk ?? '';
                                return [$mkDitawarkan->id => $namaMk];
                            });
                    })
                    ->afterStateUpdated(fn(?int $state) => $this->dispatch('mkDitawarkanIdUpdate', $state)),
            ]);
    }
}
