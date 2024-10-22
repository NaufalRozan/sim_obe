<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunajaranResource\Pages;
use App\Filament\Resources\TahunajaranResource\RelationManagers;
use App\Models\Tahunajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunajaranResource extends Resource
{
    protected static ?string $model = Tahunajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //nama tahun ajaran
                Forms\Components\TextInput::make('nama_tahun_ajaran')
                    ->label('Nama Tahun Ajaran')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //nama tahun ajaran
                Tables\Columns\TextColumn::make('nama_tahun_ajaran')
                    ->label('Nama Tahun Ajaran')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListTahunajarans::route('/'),
            'create' => Pages\CreateTahunajaran::route('/create'),
            'edit' => Pages\EditTahunajaran::route('/{record}/edit'),
        ];
    }
}
