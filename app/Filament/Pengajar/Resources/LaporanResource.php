<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\LaporanResource\Pages;
use App\Models\Laporan;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Filament\Pengajar\Resources\LaporanResource\Widgets\LaporanChart;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([])
            ->filters([
                SelectFilter::make('filter_tahun_ajaran_mk')
                    ->label('Filter Tahun Ajaran dan MK Ditawarkan')
                    ->form([
                        Grid::make(2)
                            ->schema([
                                Select::make('tahun_ajaran_id')
                                    ->label('Tahun Ajaran')
                                    ->placeholder('Pilih Tahun Ajaran')
                                    ->options(function () {
                                        return \App\Models\TahunAjaran::pluck('nama_tahun_ajaran', 'id')->toArray();
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $set('mk_ditawarkan_id', null);
                                    }),

                                Select::make('mk_ditawarkan_id')
                                    ->label('MK Ditawarkan')
                                    ->placeholder('Pilih Mata Kuliah Ditawarkan')
                                    ->options(function (callable $get) {
                                        $tahunAjaranId = $get('tahun_ajaran_id');
                                        $user = Auth::user();

                                        if ($tahunAjaranId && $user && $user->pengajar) {
                                            return \App\Models\MkDitawarkan::whereHas('pengajars', function ($query) use ($user) {
                                                $query->where('pengajar_id', $user->pengajar->id);
                                            })
                                                ->whereHas('semester', function ($query) use ($tahunAjaranId) {
                                                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                                                })
                                                ->with('mk')
                                                ->get()
                                                ->pluck('mk.nama_mk', 'id');
                                        }

                                        return [];
                                    })
                                    ->disabled(fn(callable $get) => !$get('tahun_ajaran_id'))
                                    ->reactive(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['tahun_ajaran_id'])) {
                            $query->whereHas('mkDitawarkan.semester', function (Builder $query) use ($data) {
                                $query->where('tahun_ajaran_id', $data['tahun_ajaran_id']);
                            });
                        }

                        if (isset($data['mk_ditawarkan_id'])) {
                            $query->where('mk_ditawarkan_id', $data['mk_ditawarkan_id']);
                        }
                    }),
            ], FiltersLayout::AboveContent)
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\LaporanPage::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            LaporanChart::class,
        ];
    }
}
