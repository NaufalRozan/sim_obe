<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MkResource\Pages;
use App\Filament\Resources\MkResource\RelationManagers;
use App\Models\Kurikulum;
use App\Models\Mk;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MkResource extends Resource
{
    protected static ?string $model = Mk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'MK';

    protected static ?string $navigationGroup = 'CPL';

    protected static ?string $breadcrumb = 'MK';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    // Dropdown untuk Kurikulum berdasarkan Prodi
                    Select::make('kurikulum_id')
                        ->label('Kurikulum')
                        ->options(function (callable $get) {
                            $prodi = Prodi::find($get('prodi_id'));

                            if ($prodi) {
                                return $prodi->kurikulums->pluck('nama_kurikulum', 'id');
                            }

                            return Kurikulum::all()->pluck('nama_kurikulum', 'id');
                        })
                        ->reactive()
                        ->required(),

                    // Dropdown untuk CPL berdasarkan Kurikulum
                    Select::make('cpl_ids')
                        ->label('CPL')
                        // ->multiple() // memungkinkan multiple CPL dipilih
                        ->options(function (callable $get) {
                            $kurikulum = Kurikulum::find($get('kurikulum_id'));

                            if ($kurikulum) {
                                return $kurikulum->cpls->pluck('nama_cpl', 'id');
                            }

                            return [];
                        })
                        ->multiple()
                        ->required(),
                    //semester
                    Forms\Components\TextInput::make('semester'),
                    //kode
                    Forms\Components\TextInput::make('kode'),

                    //nama_mk
                    Forms\Components\TextInput::make('nama_mk'),
                    //bobot
                    TextInput::make('bobot')
                        ->label('Bobot')
                        ->type('number')
                        ->step('0.01')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //kode
                TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_mk')
                    ->label('Mata Kuliah')
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
            'index' => Pages\ListMks::route('/'),
            'create' => Pages\CreateMk::route('/create'),
            'edit' => Pages\EditMk::route('/{record}/edit'),
        ];
    }
}
