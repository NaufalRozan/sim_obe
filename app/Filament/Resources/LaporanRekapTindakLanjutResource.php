<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanRekapTindakLanjutResource\Pages;
use App\Filament\Resources\LaporanRekapTindakLanjutResource\RelationManagers;
use App\Models\Laporan;
use App\Models\LaporanRekapTindakLanjut;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LaporanRekapTindakLanjutResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $breadcrumb = 'Laporan Rekap Tindak Lanjut';

    protected static ?string $navigationLabel = 'Laporan Rekap Tindak Lanjut';


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

                //kelas
                Tables\Columns\TextColumn::make('mkDitawarkan.kelas')
                    ->label('Matakuliah'),

                Tables\Columns\TextColumn::make('faktor_pendukung_kendala')
                    ->label('Faktor Pendukung dan Kendala')
                    ->wrap(),

                Tables\Columns\TextColumn::make('rtl')
                    ->label('RTL')
                    ->wrap(),
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
                                    ->options(function () {
                                        return \App\Models\TahunAjaran::pluck('nama_tahun_ajaran', 'id')->toArray();
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Reset MK Ditawarkan dan Kelas ketika Tahun Ajaran berubah
                                        $set('mk_ditawarkan_id', null);
                                        $set('kelas', null);
                                    }),

                                // MK Ditawarkan
                                Select::make('mk_ditawarkan_id')
                                    ->label('MK Ditawarkan')
                                    ->placeholder('Pilih MK Ditawarkan')
                                    ->options(function (callable $get) {
                                        $tahunAjaranId = $get('tahun_ajaran_id');

                                        if ($tahunAjaranId) {
                                            return \App\Models\MkDitawarkan::whereHas('semester', function ($query) use ($tahunAjaranId) {
                                                $query->where('tahun_ajaran_id', $tahunAjaranId);
                                            })
                                                ->with('mk')
                                                ->get()
                                                ->groupBy('mk.nama_mk')
                                                ->mapWithKeys(function ($groupedMkDitawarkan) {
                                                    $firstMkDitawarkan = $groupedMkDitawarkan->first();
                                                    return [$firstMkDitawarkan->mk->id => $firstMkDitawarkan->mk->nama_mk];
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
                                        $mkId = $get('mk_ditawarkan_id');

                                        if ($mkId) {
                                            return \App\Models\MkDitawarkan::where('mk_id', $mkId)
                                                ->get()
                                                ->flatMap(function ($mkDitawarkan) {
                                                    return explode(',', $mkDitawarkan->kelas);
                                                })
                                                ->unique()
                                                ->mapWithKeys(function ($kelas) {
                                                    return [$kelas => $kelas];
                                                });
                                        }

                                        return [];
                                    })
                                    ->disabled(fn(callable $get) => !$get('mk_ditawarkan_id'))
                                    ->reactive()
                                    ->searchable(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        // Jika filter belum dipilih, tampilkan data sesuai kondisi
                        if (empty($data['tahun_ajaran_id']) && empty($data['mk_ditawarkan_id']) && empty($data['kelas'])) {
                            $query->whereRaw('1 = 0'); // Tidak menampilkan data jika semua filter kosong
                            return;
                        }

                        // Filter berdasarkan Tahun Ajaran
                        if (!empty($data['tahun_ajaran_id'])) {
                            $query->whereHas('mkDitawarkan.semester', function (Builder $query) use ($data) {
                                $query->where('tahun_ajaran_id', $data['tahun_ajaran_id']);
                            });
                        }

                        // Filter berdasarkan MK Ditawarkan
                        if (!empty($data['mk_ditawarkan_id'])) {
                            $query->whereHas('mkDitawarkan', function (Builder $query) use ($data) {
                                $query->where('mk_id', $data['mk_ditawarkan_id']);
                            });
                        }

                        // Filter berdasarkan Kelas
                        if (!empty($data['kelas'])) {
                            $query->whereHas('mkDitawarkan', function (Builder $query) use ($data) {
                                $query->where('kelas', 'LIKE', "%{$data['kelas']}%");
                            });
                        }
                    }),
            ], FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListLaporanRekapTindakLanjuts::route('/'),
            // 'create' => Pages\CreateLaporanRekapTindakLanjut::route('/create'),
            // 'edit' => Pages\EditLaporanRekapTindakLanjut::route('/{record}/edit'),
        ];
    }
}
