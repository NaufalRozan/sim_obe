<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MkResource\Pages;
use App\Filament\Resources\MkResource\RelationManagers;
use App\Models\Kurikulum;
use App\Models\Mk;
use App\Models\Prodi;
use Faker\Provider\fr_CA\Text;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MkResource extends Resource
{
    protected static ?string $model = Mk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'CPL Matakuliah';

    protected static ?string $navigationGroup = 'CPL';

    protected static ?string $breadcrumb = 'CPL Matakuliah';

    protected static ?int $navigationSort = 2;

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

                    //nama_mk
                    Forms\Components\TextInput::make('nama_mk')
                        ->label('Mata Kuliah')
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

                    //semester
                    Forms\Components\TextInput::make('semester')
                        ->label('Semester')
                        ->required(),
                    //kode
                    Forms\Components\TextInput::make('kode')
                        ->label('Kode')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //semester
                TextColumn::make('semester')
                    ->label('Semester'),
                //kode
                TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable(),
                // bobot yang diambil dari total jumlah pivot cpl_mk
                TextColumn::make('total_bobot')
                    ->label('Total Bobot')
                    ->getStateUsing(function (Mk $record) {
                        // Menghitung total bobot dari tabel pivot cpl_mk
                        return $record->cpls->sum('pivot.bobot');
                    }),
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
            'index' => Pages\ListMks::route('/'),
            'create' => Pages\CreateMk::route('/create'),
            'edit' => Pages\EditMk::route('/{record}/edit'),
        ];
    }
}
