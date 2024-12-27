<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\LaporanMahasiswaResource\Pages;
use App\Filament\Pengajar\Resources\LaporanMahasiswaResource\RelationManagers;
use App\Models\LaporanMahasiswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LaporanMahasiswaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $breadcrumb = 'Laporan Mahasiswa per CPMK';

    protected static ?string $navigationLabel = 'Laporan Mahasiswa per CPMK';


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Nama Mahasiswa
                TextColumn::make('name')
                    ->label('Nama Mahasiswa')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('lulus_memuaskan')
                    ->label('Lulus Memuaskan')
                    ->getStateUsing(function (User $record) {
                        return $record->krsMahasiswas()
                            ->with(['cpmkMahasiswa.cpmk']) // Ambil relasi CPMK Mahasiswa
                            ->get()
                            ->flatMap(function ($krs) {
                                return $krs->cpmkMahasiswa->filter(function ($cpmkMahasiswa) {
                                    // Filter nilai >= batas memuaskan
                                    return $cpmkMahasiswa->nilai >= $cpmkMahasiswa->cpmk->batas_nilai_memuaskan;
                                });
                            })->count(); // Hitung jumlah nilai memuaskan
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('lulus')
                    ->label('Lulus')
                    ->getStateUsing(function (User $record) {
                        return $record->krsMahasiswas()
                            ->with(['cpmkMahasiswa.cpmk'])
                            ->get()
                            ->flatMap(function ($krs) {
                                return $krs->cpmkMahasiswa->filter(function ($cpmkMahasiswa) {
                                    // Filter nilai >= batas lulus && < batas memuaskan
                                    return $cpmkMahasiswa->nilai >= $cpmkMahasiswa->cpmk->batas_nilai_lulus &&
                                        $cpmkMahasiswa->nilai < $cpmkMahasiswa->cpmk->batas_nilai_memuaskan;
                                });
                            })->count(); // Hitung jumlah nilai lulus
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tidak_lulus')
                    ->label('Tidak Lulus')
                    ->getStateUsing(function (User $record) {
                        return $record->krsMahasiswas()
                            ->with(['cpmkMahasiswa.cpmk'])
                            ->get()
                            ->flatMap(function ($krs) {
                                return $krs->cpmkMahasiswa->filter(function ($cpmkMahasiswa) {
                                    // Filter nilai < batas lulus
                                    return $cpmkMahasiswa->nilai < $cpmkMahasiswa->cpmk->batas_nilai_lulus;
                                });
                            })->count(); // Hitung jumlah nilai tidak lulus
                    })
                    ->sortable(),

                    Tables\Columns\TextColumn::make('belum_diambil')
                    ->label('Belum Diambil')
                    ->getStateUsing(function (User $record) {
                        // Ambil semua MK yang sudah diambil user
                        $takenMks = $record->krsMahasiswas()
                            ->with(['mkDitawarkan', 'cpmkMahasiswa.cpmk']) // Ambil relasi terkait CPMK
                            ->get();

                        $takenMkIds = $takenMks
                            ->pluck('mkDitawarkan.mk_id')
                            ->unique()
                            ->toArray();

                        // Ambil semua MK dari prodi yang sama
                        $prodiIds = $record->prodis->pluck('id');
                        $allMks = \App\Models\Mk::whereHas('kurikulum.prodi', function ($query) use ($prodiIds) {
                            $query->whereIn('id', $prodiIds);
                        })
                            ->with(['cpmks'])
                            ->get();

                        $cpmkCount = 0; // Hitung total CPMK yang belum diambil

                        // Iterasi semua MK untuk cek kondisi
                        foreach ($allMks as $mk) {
                            $isTaken = in_array($mk->id, $takenMkIds);

                            if ($isTaken) {
                                // MK sudah diambil, cek apakah ada nilai CPMK
                                $cpmksBelumDinilai = $takenMks->filter(function ($krs) use ($mk) {
                                    return $krs->mkDitawarkan->mk_id === $mk->id && $krs->cpmkMahasiswa->every(function ($cpmkMahasiswa) {
                                        return is_null($cpmkMahasiswa->nilai); // Tidak ada nilai
                                    });
                                });

                                if ($cpmksBelumDinilai->isNotEmpty()) {
                                    $cpmkCount += $mk->cpmks->count(); // Tambahkan semua CPMK
                                }
                            } else {
                                // MK belum diambil, tambahkan semua CPMK ke hitungan
                                $cpmkCount += $mk->cpmks->count();
                            }
                        }

                        return $cpmkCount; // Tampilkan jumlah CPMK
                    })
                    ->sortable(),

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
                                        $set('kurikulum_id', null); // Reset kurikulum jika tahun ajaran berubah
                                    }),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        // Jika filter belum dipilih, jangan tampilkan data
                        if (!isset($data['tahun_ajaran_id'])) {
                            $query->whereRaw('1 = 0'); // Tidak menampilkan data apapun
                            return;
                        }

                        // Filter berdasarkan Tahun Ajaran
                        if (isset($data['tahun_ajaran_id'])) {
                            $query->whereHas('krsMahasiswas.mkDitawarkan.semester', function (Builder $query) use ($data) {
                                $query->where('tahun_ajaran_id', $data['tahun_ajaran_id']);
                            });
                        }
                    }),
            ], FiltersLayout::AboveContent)

            ->actions([
                Tables\Actions\Action::make('lihatDetailNilai')
                    ->label('Detail Nilai')
                    ->icon('heroicon-o-eye')
                    ->url(fn(User $record) => DetailLaporanMahasiswaPerCpmkResource::getUrl('index', [
                        'mahasiswa_id' => $record->id, // Kirim ID mahasiswa ke halaman detail
                    ])),
            ])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        // Filter only students (Mahasiswa) related to the logged-in teacher (Pengajar)
        return User::query()
            ->where('role', 'Mahasiswa')
            ->whereHas('mahasiswaPengajars', function (Builder $query) {
                $query->where('pengajar_id', Auth::id()); // Match the logged-in Pengajar ID
            });
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

        ];
    }
}
