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
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanResource extends Resource
{
    use HasFiltersForm;

    protected static ?string $model = Laporan::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // mk_ditawarkan_id - Pilih MK Ditawarkan terlebih dahulu
                Select::make('mk_ditawarkan_id')
                    ->label('MK Ditawarkan')
                    ->placeholder('Pilih MK Ditawarkan')
                    ->options(function () {
                        $user = Auth::user();

                        // Ambil semua MK Ditawarkan yang terkait dengan pengajar yang sedang login
                        return \App\Models\MkDitawarkan::whereHas('pengajars', function ($query) use ($user) {
                            $query->where('pengajar_id', $user->pengajar->id);
                        })
                            ->with('mk') // Load relasi MK agar bisa menampilkan nama MK
                            ->get()
                            ->unique('mk_id') // Menggunakan unique untuk mendapatkan satu record per mata kuliah (mk_id)
                            ->mapWithKeys(function ($mkDitawarkan) {
                                // Hanya tampilkan nama mata kuliah tanpa kelas
                                $namaMk = $mkDitawarkan->mk->nama_mk ?? '';
                                return [$mkDitawarkan->id => $namaMk];
                            });
                    })
                    ->reactive()
                    ->searchable()
                    ->preload()
                    ->required(),

                // cpmk_id - Hanya menampilkan CPMK yang terkait dengan MK Ditawarkan yang dipilih
                Select::make('cpmk_id')
                    ->label('CPMK')
                    ->placeholder('Pilih CPMK')
                    ->options(function (callable $get) {
                        $mkDitawarkanId = $get('mk_ditawarkan_id');

                        // Jika MK Ditawarkan belum dipilih, kosongkan opsi
                        if (!$mkDitawarkanId) {
                            return [];
                        }

                        // Ambil semua CPMK yang terkait dengan MK Ditawarkan yang dipilih
                        return \App\Models\Cpmk::whereHas('cplMk.mk.mkDitawarkan', function ($query) use ($mkDitawarkanId) {
                            $query->where('mk_ditawarkan.id', $mkDitawarkanId);
                        })->pluck('kode_cpmk', 'id')->toArray();
                    })
                    ->required(),

                // faktor_pendukung_kendala
                Forms\Components\Textarea::make('faktor_pendukung_kendala')
                    ->label('Faktor Pendukung dan Kendala')
                    ->placeholder('Masukkan Faktor Pendukung dan Kendala')
                    ->required(),

                // rtl
                Forms\Components\Textarea::make('rtl')
                    ->label('RTL')
                    ->placeholder('Masukkan RTL')
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cpmk.kode_cpmk')
                    ->label('CPMK'),

                Tables\Columns\TextColumn::make('mkDitawarkan.mk.nama_mk')
                    ->label('Matakuliah'),

                Tables\Columns\TextColumn::make('faktor_pendukung_kendala')
                    ->label('Faktor Pendukung dan Kendala')
                    ->wrap(),

                Tables\Columns\TextColumn::make('rtl')
                    ->label('RTL')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('filter_tahun_ajaran_mk')
                    ->label('Filter Tahun Ajaran dan MK Ditawarkan')
                    ->form([
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
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        // Menjalankan query jika filter tahun ajaran atau MK Ditawarkan dipilih
                        if (!isset($data['tahun_ajaran_id']) && !isset($data['mk_ditawarkan_id'])) {
                            // Jika tidak ada yang dipilih, jangan tampilkan data apa pun
                            $query->whereRaw('1 = 0');
                            return;
                        }

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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'edit' => Pages\EditLaporan::route('/{record}/edit'),
        ];
    }
}
