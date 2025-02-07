<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlResource\Pages;
use App\Filament\Resources\PlResource\RelationManagers;
use App\Models\Kurikulum;
use App\Models\Pl;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PlResource extends Resource
{
    protected static ?string $model = Pl::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Profil Lulusan';

    protected static ?string $navigationGroup = 'Profil Lulusan';

    protected static ?string $breadcrumb = 'Profil Lulusan';

    protected static ?string $label = 'Profil Lulusan';

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

                    //kodebk
                    TextInput::make('kode')
                        ->label('Kode Profil Lulusan')
                        ->required(),

                    //nama
                    TextInput::make('nama_pl')
                        ->label('Profil Lulusan')
                        ->required(),

                    //unsur
                    TextInput::make('unsur')
                        ->label('Unsur')
                        ->required(),

                    //sumber
                    TextInput::make('sumber')
                        ->label('Sumber')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pl')
                    ->label('Profil Lulusan')
                    ->wrap() // Agar teks turun ke baris berikutnya jika terlalu panjang
                    ->extraAttributes(['class' => 'w-96']), // Lebar kolom yang lebih besar untuk deskripsi
                Tables\Columns\TextColumn::make('unsur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sumber')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListPls::route('/'),
            'create' => Pages\CreatePl::route('/create'),
            'edit' => Pages\EditPl::route('/{record}/edit'),
        ];
    }
}
