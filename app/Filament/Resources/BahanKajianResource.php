<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanKajianResource\Pages;
use App\Filament\Resources\BahanKajianResource\RelationManagers;
use App\Models\BahanKajian;
use App\Models\Kurikulum;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BahanKajianResource extends Resource
{
    protected static ?string $model = BahanKajian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    // Dropdown untuk Kurikulum berdasarkan Prodi
                    Select::make('prodi_id')
                        ->label('Program Studi')
                        ->placeholder('Pilih Program Studi')
                        ->options(function () {
                            $user = Auth::user();
                            return $user->prodis->pluck('nama_prodi', 'id')->toArray();
                        })
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $prodi = Prodi::find($state);

                            if ($prodi) {
                                $kurikulumId = (int) $get('kurikulum_id');

                                if ($kurikulumId && $kurikulum = Kurikulum::find($kurikulumId)) {
                                    // Jika kurikulum yang dipilih tidak sesuai dengan prodi, reset kurikulum
                                    if ($kurikulum->prodi_id !== $prodi->id) {
                                        $set('kurikulum_id', null);
                                        $set('cpl_ids', null); // Reset CPL jika kurikulum direset
                                    }
                                }
                            }
                        })
                        ->reactive(),

                    Select::make('kurikulum_id')
                        ->label('Kurikulum')
                        ->options(function (callable $get) {
                            $prodi = Prodi::find($get('prodi_id'));

                            if ($prodi) {
                                return $prodi->kurikulums->pluck('nama_kurikulum', 'id');
                            }

                            return Kurikulum::all()->pluck('nama_kurikulum', 'id');
                        })
                        ->disabled(function (callable $get) {
                            // Disable jika Prodi belum dipilih
                            return is_null($get('prodi_id'));
                        })
                        ->reactive()
                        ->required(),

                    Select::make('cpl_ids')
                        ->label('CPL')
                        ->options(function (callable $get) {
                            $kurikulum = Kurikulum::find($get('kurikulum_id'));

                            if ($kurikulum) {
                                return $kurikulum->cpls->pluck('nama_cpl', 'id');
                            }

                            return [];
                        })
                        ->disabled(function (callable $get) {
                            // Disable jika Kurikulum belum dipilih
                            return is_null($get('kurikulum_id'));
                        })
                        ->relationship('cpls', 'nama_cpl', function (Builder $query, callable $get) {
                            $kurikulumId = $get('kurikulum_id');
                            if ($kurikulumId) {
                                $query->where('kurikulum_id', $kurikulumId);
                            }
                        })
                        ->multiple()
                        ->preload()
                        ->reactive()
                        ->required(),

                    //kajian
                    Forms\Components\TextInput::make('kajian')
                        ->label('Kajian')
                        ->required(),


                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kajian')
                    ->searchable()
                    ->sortable(),

                // CPL yang terhubung dengan mata kuliah
                TextColumn::make('cpls')
                    ->label('CPL')
                    ->formatStateUsing(function ($record) {
                        return $record->cpls->pluck('nama_cpl')->implode(', ');
                    })
                    ->extraAttributes(['class' => 'w-64']), // Lebar kolom diatur dengan class w-
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
            'index' => Pages\ListBahanKajians::route('/'),
            'create' => Pages\CreateBahanKajian::route('/create'),
            'edit' => Pages\EditBahanKajian::route('/{record}/edit'),
        ];
    }
}
