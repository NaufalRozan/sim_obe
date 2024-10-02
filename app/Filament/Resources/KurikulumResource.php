<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KurikulumResource\Pages;
use App\Filament\Resources\KurikulumResource\RelationManagers;
use App\Models\Kurikulum;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class KurikulumResource extends Resource
{
    protected static ?string $model = Kurikulum::class;

    protected static ?string $navigationLabel = 'Kurikulum';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    //mengganti nama breadcrumb
    protected static ?string $breadcrumb = 'Kurikulum';

    public static function form(Form $form): Form

    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kurikulum')
                    ->label('Nama Kurikulum')
                    ->required(),
                // membuat dropdown prodi sesuai dengan prodi yang terhubung dengan user_prodi
                Forms\Components\Select::make('prodi_id')
                    ->label('Program Studi')
                    ->options(function () {
                        // Mendapatkan data prodi user yang sedang login
                        $user = Auth::user();
                        return $user->prodis->pluck('nama_prodi', 'id')->toArray();
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //hanya menampilkan data kurikulum yang berelasi dengan prodi user
                Tables\Columns\TextColumn::make('nama_kurikulum')
                    ->searchable()
                    ->label('Nama Kurikulum'),
                Tables\Columns\TextColumn::make('prodi.nama_prodi')
                    ->label('Prodi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('prodi_id')
                    ->label('Program Studi')
                    ->options(function () {
                        // Mendapatkan data prodi user yang sedang login
                        $user = Auth::user();
                        return $user->prodis->pluck('nama_prodi', 'id')->toArray();
                    })
                    ->placeholder('Pilih Program Studi')
                    ->query(function (Builder $query, array $data) {
                        return $query->where('prodi_id', $data['value']);
                    })
            ], layout: FiltersLayout::AboveContent)
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
            'index' => Pages\ListKurikulums::route('/'),
            'create' => Pages\CreateKurikulum::route('/create'),
            'edit' => Pages\EditKurikulum::route('/{record}/edit'),
        ];
    }
}
