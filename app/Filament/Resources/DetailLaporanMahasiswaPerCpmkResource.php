<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource\Pages;
use App\Filament\Resources\DetailLaporanMahasiswaPerCpmkResource\RelationManagers;
use App\Models\DetailLaporanMahasiswaPerCpmk;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailLaporanMahasiswaPerCpmkResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $breadcrumb = 'Detail Laporan Mahasiswa per CPMK';

    protected static ?string $navigationLabel = 'Detail Laporan Mahasiswa per CPMK';

    protected static bool $shouldRegisterNavigation = false; // Tidak muncul di navigasi utama

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
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        $mahasiswaId = request()->query('mahasiswa_id'); // Ambil ID mahasiswa dari URL parameter

        return User::query()
            ->where('role', 'Mahasiswa') // Hanya mahasiswa
            ->when($mahasiswaId, function ($query) use ($mahasiswaId) {
                $query->where('id', $mahasiswaId); // Filter berdasarkan ID mahasiswa
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
            'index' => Pages\CustomTable::route('/'),
        ];
    }
}
