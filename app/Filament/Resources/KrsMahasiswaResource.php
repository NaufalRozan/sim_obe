<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KrsMahasiswaResource\Pages;
use App\Filament\Resources\KrsMahasiswaResource\RelationManagers;
use App\Models\KrsMahasiswa;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;

class KrsMahasiswaResource extends Resource
{
    protected static ?string $model = KrsMahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mahasiswa';

    protected static ?string $breadcrumb = 'KRS Mahasiswa';

    protected static ?string $navigationLabel = 'KRS Mahasiswa';

    protected static ?string $label = 'KRS Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Dropdown User (hanya menampilkan mahasiswa terkait prodi user yang sedang login)
                Select::make('user_id')
                    ->label('Mahasiswa')
                    ->options(function () {
                        $user = Auth::user(); // Mendapatkan user yang login
                        $prodiIds = $user->prodis->pluck('id'); // Prodi terkait user

                        // Mengambil mahasiswa dari prodi terkait
                        return \App\Models\User::where('role', 'Mahasiswa')
                            ->whereHas('prodis', function (Builder $query) use ($prodiIds) {
                                $query->whereIn('prodis.id', $prodiIds); // Tentukan prodi.id agar tidak ambigu
                            })
                            ->pluck('name', 'id'); // Menampilkan nama di dropdown
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('mk_ditawarkan_id')
                    ->label('Mata Kuliah Ditawarkan')
                    ->options(function () {
                        $user = Auth::user(); // Dapatkan user yang sedang login
                        $prodiIds = $user->prodis->pluck('id'); // Ambil semua prodi terkait user

                        // Ambil MK Ditawarkan yang sesuai dengan prodi user yang login
                        return \App\Models\MkDitawarkan::whereHas('mk.kurikulum.prodi', function ($query) use ($prodiIds) {
                            $query->whereIn('id', $prodiIds); // Filter berdasarkan prodi
                        })
                            ->with('mk') // Load relasi MK agar bisa menampilkan nama MK
                            ->get()
                            ->sortBy(function ($mkDitawarkan) {
                                return $mkDitawarkan->mk->nama_mk ?? ''; // Sort by nama_mk
                            })
                            ->mapWithKeys(function ($mkDitawarkan) {
                                // Format nama menjadi "nama_mk - kelas"
                                $namaMkDanKelas = ($mkDitawarkan->mk->nama_mk ?? '') . ' - ' . $mkDitawarkan->kelas;
                                return [$mkDitawarkan->id => $namaMkDanKelas];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('Mahasiswa'),

                Tables\Columns\TextColumn::make('user.nim')
                    ->sortable()
                    ->searchable()
                    ->label('NIM'),

                Tables\Columns\TextColumn::make('mkDitawarkan.mk.nama_mk')
                    ->searchable()
                    ->label('Mata Kuliah'),

                Tables\Columns\TextColumn::make('mkDitawarkan.kelas')
                    ->sortable()
                    ->label('Kelas'),
            ])
            ->defaultSort('user.nim', 'asc')
            ->filters([
                //filter berdasarkan nama user
                SelectFilter::make('user_id')
                    ->label('Mahasiswa')
                    ->options(function () {
                        $user = Auth::user(); // Dapatkan user yang sedang login
                        $prodiIds = $user->prodis->pluck('id'); // Ambil semua prodi terkait user

                        // Ambil mahasiswa yang sesuai dengan prodi user yang login
                        return \App\Models\User::where('role', 'Mahasiswa')
                            ->whereHas('prodis', function (Builder $query) use ($prodiIds) {
                                $query->whereIn('prodis.id', $prodiIds); // Filter berdasarkan prodi
                            })
                            ->pluck('name', 'id'); // Menampilkan nama di dropdown
                    })
                    ->searchable()
                    ->preload(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListKrsMahasiswas::route('/'),
            'create' => Pages\CreateKrsMahasiswa::route('/create'),
            'edit' => Pages\EditKrsMahasiswa::route('/{record}/edit'),
        ];
    }
}
