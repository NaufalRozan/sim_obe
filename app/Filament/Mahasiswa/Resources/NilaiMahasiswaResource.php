<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\NilaiMahasiswaResource\Pages;
use App\Models\Mk;
use App\Models\CpmkMahasiswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class NilaiMahasiswaResource extends Resource
{
    protected static ?string $model = Mk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $breadcrumb = 'Transkrip Nilai';

    protected static ?string $label = 'Transkrip Nilai';

    protected static ?string $navigationLabel = 'Transkrip Nilai';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_mk')
                    ->label('Nama MK')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir')
                    ->getStateUsing(function (Mk $record) {
                        $user = Auth::user(); // Mahasiswa yang login
                        if (!$user) {
                            return 'Tidak ada data';
                        }

                        // Hitung nilai akhir berdasarkan bobot
                        return $record->cpmks->map(function ($cpmk) use ($user) {
                            $nilaiCpmk = CpmkMahasiswa::whereHas('krsMahasiswa', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })->where('cpmk_id', $cpmk->id)->first();

                            return $nilaiCpmk ? ($nilaiCpmk->nilai * $cpmk->bobot) / 100 : 0;
                        })->sum();
                    })
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('lihatDetail')
                    ->label('Lihat Detail CPMK')
                    ->icon('heroicon-o-eye')
                    ->action(function ($record) {
                        $mkDitawarkanId = $record->mkditawarkan->id;
                        session(['selected_mk_ditawarkan_id' => $mkDitawarkanId]);

                        return redirect()->to(LihatDetailMahasiswaResource::getUrl('index', [
                            'mk_ditawarkan_id' => $mkDitawarkanId,
                        ]));
                    }),
            ])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user(); // Mahasiswa yang login

        return Mk::query()
            ->whereHas('mkditawarkan.krsMahasiswas', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
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
