<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource\Pages;
use App\Filament\Mahasiswa\Resources\DetailNilaiPerCpmkResource\RelationManagers;
use App\Models\DetailNilaiPerCpmk;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailNilaiPerCpmkResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $breadcrumb = 'Detail Nilai per CPMK';

    protected static ?string $navigationLabel = 'Detail Nilai per CPMK';

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
                        $takenMkIds = $record->krsMahasiswas()
                            ->with('mkDitawarkan')
                            ->get()
                            ->pluck('mkDitawarkan.mk_id')
                            ->unique()
                            ->toArray();

                        // Ambil semua MK dari prodi yang sama, tetapi belum diambil
                        $prodiIds = $record->prodis->pluck('id'); // Prodi user
                        $unTakenMks = \App\Models\Mk::whereHas('kurikulum.prodi', function ($query) use ($prodiIds) {
                            $query->whereIn('id', $prodiIds);
                        })
                            ->whereNotIn('id', $takenMkIds)
                            ->with(['cpmks.cplMk.mkditawarkan'])
                            ->get();

                        // Format CPMK dari MK yang belum diambil
                        return $unTakenMks->flatMap(function ($mk) {
                            return $mk->cpmks->map(function ($cpmk) use ($mk) {
                                $kelas = $mk->mkditawarkan->first()->kelas ?? 'Tidak ada kelas';
                                return $mk->kode . ' - ' . $cpmk->kode_cpmk . ' - ' . $cpmk->deskripsi;
                            });
                        })->join('<br>'); // Gabungkan daftar CPMK menjadi string dengan <br> untuk baris baru
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
            'index' => Pages\ListDetailNilaiPerCpmks::route('/'),

        ];
    }
}
