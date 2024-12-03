<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\NilaiMahasiswaResource\Pages;
use App\Models\Mk;
use App\Models\CpmkMahasiswa;
use App\Models\KrsMahasiswa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NilaiMahasiswaResource extends Resource
{
    protected static ?string $model = Mk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $breadcrumb = 'Nilai';

    protected static ?string $label = 'Nilai';

    protected static ?string $navigationLabel = 'Nilai';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Nama MK
                TextColumn::make('nama_mk')
                    ->label('Nama MK')
                    ->getStateUsing(fn(Mk $record) => $record->nama_mk)
                    ->sortable()
                    ->searchable(),

                // Nilai Akhir
                TextColumn::make('nilai_akhir')
                    ->label('Nilai')
                    ->getStateUsing(function (Mk $record) {
                        $user = Auth::user(); // Mahasiswa yang login
                        if (!$user) {
                            return 'Tidak ada data';
                        }

                        // Ambil nilai CPMK mahasiswa
                        $nilaiAkhir = $record->cpmks->map(function ($cpmk) use ($user) {
                            $nilaiCpmk = CpmkMahasiswa::whereHas('krsMahasiswa', function ($query) use ($user) {
                                $query->where('user_id', $user->id); // Hanya nilai mahasiswa yang login
                            })->where('cpmk_id', $cpmk->id)->first();

                            if ($nilaiCpmk) {
                                // Kalkulasi nilai berdasarkan bobot
                                return ($nilaiCpmk->nilai * $cpmk->bobot) / 100;
                            }

                            return 0;
                        })->sum(); // Jumlahkan semua nilai CPMK setelah dikalikan bobot

                        return number_format($nilaiAkhir, 2); // Format nilai akhir dengan 2 desimal
                    })
                    ->sortable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user(); // Mahasiswa yang login

        // Filter hanya MK yang sudah diambil oleh mahasiswa
        return Mk::query()
            ->whereHas('mkditawarkan.krsMahasiswas', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id); // Hanya MK yang sudah diambil oleh mahasiswa
            });
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNilaiMahasiswas::route('/'),
        ];
    }
}
