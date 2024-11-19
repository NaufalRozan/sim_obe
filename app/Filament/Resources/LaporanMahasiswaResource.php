<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanMahasiswaResource\Pages;
use App\Filament\Resources\LaporanMahasiswaResource\RelationManagers;
use App\Models\Cpl;
use App\Models\KrsMahasiswa;
use App\Models\LaporanMahasiswa;
use App\Models\MkDitawarkan;
use App\Models\TahunAjaran;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

class LaporanMahasiswaResource extends Resource
{
    protected static ?string $model = KrsMahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $breadcrumb = 'Laporan Mahasiswa';

    protected static ?string $navigationLabel = 'Laporan Mahasiswa';

    protected static ?string $label = 'Laporan Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //nama mahasiswa
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Mahasiswa'),

                // Nama mata kuliah yang diambil dari `mk` melalui `mk_ditawarkan`
                Tables\Columns\TextColumn::make('mkDitawarkan.mk.nama_mk')
                    ->label('Mata Kuliah Ditawarkan'),

                //kelas
                Tables\Columns\TextColumn::make('mkDitawarkan.kelas')
                    ->label('Kelas'),

            ])
            ->filters([
                SelectFilter::make('filter_tahun_ajaran_mk_kelas')
                    ->label('Filter Tahun Ajaran, MK Ditawarkan, dan Kelas')
                    ->form([
                        Grid::make(3)
                            ->schema([
                                // Tahun Ajaran
                                Select::make('tahun_ajaran_id')
                                    ->label('Tahun Ajaran')
                                    ->placeholder('Pilih Tahun Ajaran')
                                    ->options(TahunAjaran::pluck('nama_tahun_ajaran', 'id')->toArray())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('mk_ditawarkan_id', null);
                                        $set('kelas', null);
                                    }),

                                // MK Ditawarkan (hanya satu per mata kuliah)
                                Select::make('mk_ditawarkan_id')
                                    ->label('MK Ditawarkan')
                                    ->placeholder('Pilih MK Ditawarkan')
                                    ->options(function (callable $get) {
                                        $tahunAjaranId = $get('tahun_ajaran_id');

                                        if ($tahunAjaranId) {
                                            return MkDitawarkan::whereHas('semester', function ($query) use ($tahunAjaranId) {
                                                $query->where('tahun_ajaran_id', $tahunAjaranId);
                                            })
                                                ->with('mk')
                                                ->get()
                                                ->unique('mk_id')
                                                ->mapWithKeys(function ($mkDitawarkan) {
                                                    return [$mkDitawarkan->id => $mkDitawarkan->mk->nama_mk];
                                                });
                                        }

                                        return [];
                                    })
                                    ->disabled(fn(callable $get) => !$get('tahun_ajaran_id'))
                                    ->reactive()
                                    ->searchable()
                                    ->preload()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('kelas', null);
                                    }),

                                // Kelas
                                Select::make('kelas')
                                    ->label('Kelas')
                                    ->placeholder('Pilih Kelas')
                                    ->options(function (callable $get) {
                                        $mkDitawarkanId = $get('mk_ditawarkan_id');

                                        if ($mkDitawarkanId) {
                                            return MkDitawarkan::where('mk_id', MkDitawarkan::find($mkDitawarkanId)->mk_id)
                                                ->pluck('kelas', 'kelas')
                                                ->toArray();
                                        }

                                        return [];
                                    })
                                    ->disabled(fn(callable $get) => !$get('mk_ditawarkan_id'))
                                    ->reactive(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        // Jika salah satu filter belum dipilih, jangan tampilkan data
                        if (!isset($data['tahun_ajaran_id']) || !isset($data['mk_ditawarkan_id']) || !isset($data['kelas'])) {
                            $query->whereRaw('1 = 0'); // Tidak menampilkan data apapun
                            return;
                        }

                        // Filter berdasarkan Tahun Ajaran
                        if (isset($data['tahun_ajaran_id'])) {
                            $query->whereHas('mkDitawarkan.semester', function (Builder $query) use ($data) {
                                $query->where('tahun_ajaran_id', $data['tahun_ajaran_id']);
                            });
                        }

                        // Filter berdasarkan MK Ditawarkan
                        if (isset($data['mk_ditawarkan_id'])) {
                            $mkId = MkDitawarkan::find($data['mk_ditawarkan_id'])->mk_id ?? null;
                            if ($mkId) {
                                $query->whereHas('mkDitawarkan', function (Builder $query) use ($mkId) {
                                    $query->where('mk_id', $mkId);
                                });
                            }
                        }

                        // Filter berdasarkan Kelas
                        if (isset($data['kelas'])) {
                            $query->whereHas('mkDitawarkan', function (Builder $query) use ($data) {
                                $query->where('kelas', $data['kelas']);
                            });
                        }
                    }),
            ], FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\Action::make('lihatMahasiswa')
                //     ->label('Lihat Laporan')
                //     ->icon('heroicon-o-eye')
                //     ->action(function ($record) {
                //         session(['mk_ditawarkan_id' => $record->id]); // Simpan di session
                //         return redirect()->to(CpmkMahasiswaResource::getUrl('index', [
                //             'mk_ditawarkan_id' => $record->id,
                //         ]));
                //     }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanMahasiswas::route('/'),
            'create' => Pages\CreateLaporanMahasiswa::route('/create'),
            'edit' => Pages\EditLaporanMahasiswa::route('/{record}/edit'),
        ];
    }
}
