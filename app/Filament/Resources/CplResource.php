<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CplResource\Pages;
use App\Filament\Resources\CplResource\RelationManagers;
use App\Filament\Resources\CplResource\RelationManagers\CplIndikatorRelationManager;
use App\Models\Cpl;
use App\Models\Kurikulum;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CplResource extends Resource
{
    protected static ?string $model = Cpl::class;

    protected static ?string $title = 'Finance dashboard';

    protected static ?string $navigationLabel = 'CPL Prodi';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'CPL';

    protected static ?string $breadcrumb = 'CPL Prodi';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
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
                                    }
                                }
                            }
                        })
                        ->reactive(),
                    // Dropdown untuk memilih Kurikulum
                    Select::make('kurikulum_id')
                        ->label('Kurikulum')
                        ->placeholder('Pilih Kurikulum')
                        ->options(function (callable $get) {
                            $prodi = Prodi::find($get('prodi_id'));

                            if ($prodi) {
                                // Jika prodi dipilih, ambil kurikulum yang sesuai dengan prodi
                                return $prodi->kurikulums->pluck('nama_kurikulum', 'id');
                            }

                            // Jika tidak ada prodi yang dipilih, tampilkan semua kurikulum
                            return Kurikulum::all()->pluck('nama_kurikulum', 'id');
                        })
                        ->required()
                        ->reactive(),
                    Forms\Components\TextInput::make('nama_cpl')
                        ->label('Kode')
                        ->required(),
                    Forms\Components\TextInput::make('cpl_ke')
                        ->label('CPL Ke')
                        ->required(),
                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->required()
                        ->columnSpanFull()
                ])->Columns(2)
            ])->Columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // menampilkan CPL hanya yang berelasi dengan kurikulum user
                Tables\Columns\TextColumn::make('nama_cpl')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kurikulum.prodi.nama_prodi')
                    ->label('Program Studi'),
                Tables\Columns\TextColumn::make('kurikulum.nama_kurikulum')
                    ->label('Kurikulum'),
                Tables\Columns\TextColumn::make('cpl_ke')
                    ->label('CPL Ke'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi'),
            ])
            ->filters([
                // Filter kurikulum dengan form custom
                SelectFilter::make('kurikulum')
                    ->label('Kurikulum')
                    ->form([
                        Grid::make(2) // Membuat grid dengan 2 kolom
                            ->schema(
                                [
                                    // Dropdown untuk memilih Program Studi
                                    Select::make('prodi_id')
                                        ->label('Program Studi')
                                        ->placeholder('Pilih Program Studi')
                                        // Mendapatkan user yang sedang login
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
                                                    }
                                                }
                                            }
                                        })
                                        ->reactive(),

                                    // Dropdown untuk memilih Kurikulum
                                    Select::make('kurikulum_id')
                                        ->label('Kurikulum')
                                        ->placeholder('Pilih Kurikulum')
                                        ->options(function (callable $get) {
                                            $prodi = Prodi::find($get('prodi_id'));

                                            if ($prodi) {
                                                // Jika prodi dipilih, ambil kurikulum yang sesuai dengan prodi
                                                return $prodi->kurikulums->pluck('nama_kurikulum', 'id');
                                            }

                                            // Jika tidak ada prodi yang dipilih, kosongkan pilihan kurikulum
                                            return [];
                                        })
                                        ->disabled(function (callable $get) {
                                            // Disable dropdown jika Prodi belum dipilih
                                            return is_null($get('prodi_id'));
                                        })
                                        ->reactive(),
                                ]
                            ),
                    ])->columnSpanFull()
                    // Query for filtering the data based on the selected prodi and kurikulum
                    ->query(function (Builder $query, array $data) {
                        // If neither prodi_id nor kurikulum_id is set, return no data (empty result)
                        if (!isset($data['prodi_id']) && !isset($data['kurikulum_id'])) {
                            $query->whereRaw('1 = 0');  // This will force the query to return no results
                        }

                        // If prodi_id is selected, filter by prodi_id
                        if (isset($data['prodi_id'])) {
                            $query->whereHas('kurikulum', function ($query) use ($data) {
                                $query->where('prodi_id', $data['prodi_id']);
                            });
                        }

                        // If kurikulum_id is selected, filter by kurikulum_id
                        if (isset($data['kurikulum_id'])) {
                            $query->where('kurikulum_id', $data['kurikulum_id']);
                        }
                    }),
            ], FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
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
