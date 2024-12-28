<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanMahasiswaResource\Pages;
use App\Models\Kurikulum;
use App\Models\TahunAjaran;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanMahasiswaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $breadcrumb = 'Laporan Mahasiswa Per CPMK';

    protected static ?string $navigationLabel = 'Laporan Mahasiswa Per CPMK';

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
                            ->with(['mkDitawarkan', 'cpmkMahasiswa.cpmk.cplMk.mk'])
                            ->get()
                            ->flatMap(function ($krs) {
                                return $krs->cpmkMahasiswa->filter(function ($cpmkMahasiswa) {
                                    // Filter data berdasarkan nilai >= batas_nilai_memuaskan
                                    return $cpmkMahasiswa->nilai >= $cpmkMahasiswa->cpmk->batas_nilai_memuaskan;
                                })->map(function ($cpmkMahasiswa) use ($krs) {
                                    // Ambil kelas dari MkDitawarkan
                                    $kelas = $krs->mkDitawarkan->kelas ?? 'Tidak ada kelas';

                                    // Format data: kode CPMK - nama MK - kelas
                                    return $cpmkMahasiswa->cpmk->cplMk->mk->kode . ' - ' // Kode MK
                                        . $cpmkMahasiswa->cpmk->kode_cpmk . ' - ' // Nama MK
                                        . $cpmkMahasiswa->cpmk->deskripsi;
                                });
                            })->join('<br>'); // Gabungkan daftar CPMK dengan <br> untuk tampil sebagai daftar
                    })
                    ->html()
                    ->toggleable(),


                Tables\Columns\TextColumn::make('lulus')
                    ->label('Lulus')
                    ->getStateUsing(function (User $record) {
                        return $record->krsMahasiswas()
                            ->with(['mkDitawarkan', 'cpmkMahasiswa.cpmk.cplMk.mk'])
                            ->get()
                            ->flatMap(function ($krs) {
                                return $krs->cpmkMahasiswa->filter(function ($cpmkMahasiswa) {
                                    // Filter data untuk nilai < batas_nilai_memuaskan && >= batas_nilai_lulus
                                    return $cpmkMahasiswa->nilai < $cpmkMahasiswa->cpmk->batas_nilai_memuaskan &&
                                        $cpmkMahasiswa->nilai >= $cpmkMahasiswa->cpmk->batas_nilai_lulus;
                                })->map(function ($cpmkMahasiswa) use ($krs) {
                                    $kelas = $krs->mkDitawarkan->kelas ?? 'Tidak ada kelas';
                                    return $cpmkMahasiswa->cpmk->cplMk->mk->kode . ' - ' // Kode MK
                                        . $cpmkMahasiswa->cpmk->kode_cpmk . ' - ' // Nama MK
                                        . $cpmkMahasiswa->cpmk->deskripsi;
                                });
                            })->join('<br>');
                    })
                    ->html()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tidak_lulus')
                    ->label('Tidak Lulus')
                    ->getStateUsing(function (User $record) {
                        return $record->krsMahasiswas()
                            ->with(['mkDitawarkan', 'cpmkMahasiswa.cpmk.cplMk.mk'])
                            ->get()
                            ->flatMap(function ($krs) {
                                return $krs->cpmkMahasiswa->filter(function ($cpmkMahasiswa) {
                                    // Filter data untuk nilai < batas_nilai_lulus
                                    return $cpmkMahasiswa->nilai < $cpmkMahasiswa->cpmk->batas_nilai_lulus;
                                })->map(function ($cpmkMahasiswa) use ($krs) {
                                    $kelas = $krs->mkDitawarkan->kelas ?? 'Tidak ada kelas';
                                    return $cpmkMahasiswa->cpmk->cplMk->mk->kode . ' - ' // Kode MK
                                        . $cpmkMahasiswa->cpmk->kode_cpmk . ' - ' // Nama MK
                                        . $cpmkMahasiswa->cpmk->deskripsi;
                                });
                            })->join('<br>');
                    })
                    ->html()->toggleable(),



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

                        $belumDiambil = [];

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
                                    foreach ($mk->cpmks as $cpmk) {
                                        $belumDiambil[] = $mk->kode . ' - ' . $cpmk->kode_cpmk . ' - ' . $cpmk->deskripsi;
                                    }
                                }
                            } else {
                                // MK belum diambil, masukkan semua CPMK ke kategori
                                foreach ($mk->cpmks as $cpmk) {
                                    $belumDiambil[] = $mk->kode . ' - ' . $cpmk->kode_cpmk . ' - ' . $cpmk->deskripsi;
                                }
                            }
                        }

                        // Gabungkan daftar CPMK menjadi string dengan <br>
                        return implode('<br>', $belumDiambil);
                    })
                    ->html()
                    ->toggleable(),

            ])
            ->filters([
                SelectFilter::make('filter_tahun_ajaran_mk_kelas')
                    ->label('Filter Tahun Ajaran dan MK Ditawarkan')
                    ->form([
                        Grid::make(1) // Grid dengan satu baris untuk memuat filter ini
                            ->schema([
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
                    ->query(function (Builder $query, array $data) {
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

                SelectFilter::make('filter_angkatan')
                    ->label('Filter Angkatan')
                    ->form([
                        Grid::make(1) // Grid dengan satu baris untuk memuat filter ini
                            ->schema([
                                Select::make('angkatan')
                                    ->label('Angkatan')
                                    ->placeholder('Pilih Angkatan')
                                    ->options(
                                        array_combine(range(2000, date('Y')), range(2000, date('Y')))
                                    )
                                    ->reactive(),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['angkatan'])) {
                            $query->where('angkatan', $data['angkatan']);
                        }
                    })
            ], FiltersLayout::AboveContent)

            ->actions([])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        // Filter hanya mahasiswa dengan prodi terkait admin
        return User::query()
            ->where('role', 'Mahasiswa')
            ->whereHas('prodis', function (Builder $query) {
                $query->whereIn('prodi_id', Auth::user()->prodis->pluck('id'));
            });
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanMahasiswas::route('/'),
        ];
    }
}
