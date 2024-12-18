<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DPAResource\Pages;
use App\Models\MahasiswaPengajar;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DPAResource extends Resource
{
    protected static ?string $model = MahasiswaPengajar::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'DPA';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Dropdown Pengajar
                Select::make('pengajar_id')
                    ->label('Pengajar')
                    ->options(User::where('role', 'Dosen')->pluck('name', 'id')) // Hanya Dosen
                    ->searchable()
                    ->reactive() // Membuat reaktif untuk memfilter mahasiswa
                    ->required(),

                // Dropdown Mahasiswa
                Select::make('mahasiswa_id')
                    ->label('Mahasiswa')
                    ->options(function (callable $get) {
                        $pengajarId = $get('pengajar_id'); // Ambil pengajar yang dipilih
                        if ($pengajarId) {
                            // Cari Prodi pengajar yang dipilih
                            $prodiIds = User::find($pengajarId)?->prodis->pluck('id');

                            // Filter mahasiswa berdasarkan Prodi pengajar
                            return User::where('role', 'Mahasiswa')
                                ->whereHas('prodis', function ($query) use ($prodiIds) {
                                    $query->whereIn('prodis.id', $prodiIds); // Tentukan nama tabel untuk id
                                })
                                ->pluck('name', 'id');
                        }

                        // Jika pengajar belum dipilih, kosongkan pilihan mahasiswa
                        return [];
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pengajar.name')
                    ->label('Nama Pengajar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pengajar.email')
                    ->label('Email Pengajar')
                    ->searchable(),

                TextColumn::make('mahasiswa.name')
                    ->label('Nama Mahasiswa')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDPAS::route('/'),
            'create' => Pages\CreateDPA::route('/create'),
            'edit' => Pages\EditDPA::route('/{record}/edit'),
        ];
    }
}
