<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\CpmkMahasiswaResource\Pages;
use App\Models\Cpmk;
use App\Models\CpmkMahasiswa;
use App\Models\KrsMahasiswa;
use App\Models\MkDitawarkan;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CpmkMahasiswaResource extends Resource
{
    protected static ?string $model = KrsMahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static bool $shouldRegisterNavigation = false; // Tidak muncul di navigasi utama

    public static function table(Table $table): Table
    {
        $mkDitawarkanId = request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id'); // Ambil dari request atau session

        if (!$mkDitawarkanId) {
            \Filament\Notifications\Notification::make()
                ->title('MK Ditawarkan tidak ditemukan')
                ->warning()
                ->send();
            return $table;
        }

        // Query untuk mendapatkan CPMK terkait
        $cpmks = Cpmk::whereHas('cplMk', function ($query) use ($mkDitawarkanId) {
            $query->whereHas('mkDitawarkan', function ($subQuery) use ($mkDitawarkanId) {
                $subQuery->where('mk_ditawarkan.id', $mkDitawarkanId);
            });
        })->get();

        $columns = [
            TextColumn::make('user.name')
                ->label('Nama Mahasiswa')
                ->sortable()
                ->searchable(),
            TextColumn::make('user.nim')
                ->label('NIM')
                ->sortable()
                ->searchable(),
        ];

        // Tambahkan kolom CPMK terkait secara dinamis
        foreach ($cpmks as $cpmk) {
            $columns[] = TextColumn::make("cpmkMahasiswa.{$cpmk->id}.nilai")
                ->label("{$cpmk->kode_cpmk}")
                ->getStateUsing(function ($record) use ($cpmk) {
                    $cpmkMahasiswa = $record->cpmkMahasiswa->firstWhere('cpmk_id', $cpmk->id);
                    return $cpmkMahasiswa ? $cpmkMahasiswa->nilai : '-';
                })
                ->searchable(); // Hanya searchable, tidak sortable
        }
        return $table
            ->columns($columns)
            ->filters([
                Tables\Filters\SelectFilter::make('mk_ditawarkan_id')
                    ->label('Mata Kuliah')
                    ->options(MkDitawarkan::all()->pluck('mk.nama_mk', 'id'))
                    ->default(fn() => request()->query('mk_ditawarkan_id'))
                    ->query(function (Builder $query, array $data) {
                        $query->where('mk_ditawarkan_id', $data['value']);
                    }),
            ])
            ->actions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCpmkMahasiswas::route('/'),
            'edit' => Pages\EditCpmkMahasiswa::route('/{record}/edit'),
        ];
    }
}
