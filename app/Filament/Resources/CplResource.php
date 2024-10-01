<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CplResource\Pages;
use App\Filament\Resources\CplResource\RelationManagers;
use App\Models\Cpl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CplResource extends Resource
{
    protected static ?string $model = Cpl::class;

    protected static ?string $navigationLabel = 'CPL';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $breadcrumb = 'CPL';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_cpl')
                    ->label('Nama CPL')
                    ->required(),
                //dropdown hanya menampilkan kurikulum yang berelasi dengan prodi user_prodi
                Forms\Components\Select::make('kurikulum_id')
                    ->label('Kurikulum')
                    ->options(function () {
                        // Mendapatkan user yang sedang login
                        $user = Auth::user();

                        // Mendapatkan semua kurikulum yang berelasi dengan prodi user dan mengembalikan key-value pair yang benar
                        return $user->prodis->mapWithKeys(function ($prodi) {
                            return $prodi->kurikulums->pluck('nama_kurikulum', 'id');
                        })->toArray();  // Menghasilkan array dengan 'id' sebagai key dan 'nama_kurikulum' sebagai value
                    })
                    ->required()
                    ->placeholder('Pilih Kurikulum')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // menampilkan CPL hanya yang berelasi dengan kurikulum user
                Tables\Columns\TextColumn::make('nama_cpl')
                    ->label('Nama CPL'),
                Tables\Columns\TextColumn::make('kurikulum.nama_kurikulum')

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCpls::route('/'),
            'create' => Pages\CreateCpl::route('/create'),
            'edit' => Pages\EditCpl::route('/{record}/edit'),
        ];
    }
}
