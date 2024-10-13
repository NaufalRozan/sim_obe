<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CpmkResource\Pages;
use App\Filament\Resources\CpmkResource\RelationManagers;
use App\Filament\Resources\CpmkResource\RelationManagers\CplmkRelationManager;
use App\Filament\Resources\CpmkResource\RelationManagers\CplRelationManager;
use App\Filament\Resources\CpmkResource\RelationManagers\CpmksRelationManager;
use App\Filament\Resources\CpmkResource\RelationManagers\MkRelationManager;
use App\Models\Cpmk;
use App\Models\Kurikulum;
use App\Models\Mk;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CpmkResource extends Resource
{
    protected static ?string $model = Mk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'CPMK';

    protected static ?string $breadcrumb = 'CPMK';

    protected static ?string $navigationLabel = 'CPMK';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // kode MK
                Forms\Components\TextInput::make('kode')
                    ->label('Kode Matakuliah')
                    ->disabled(),
                //nama MK
                Forms\Components\TextInput::make('nama_mk')
                    ->label('Nama Matakuliah')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //semester
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester'),
                //kode_mk
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode MK'),
                //nama_mk
                Tables\Columns\TextColumn::make('nama_mk')
                    ->label('Nama MK'),
                //rps cpmk
                Tables\Columns\TextColumn::make('rps')
                    ->label('RPS'),
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
                // Filter semester (Dinamis berdasarkan data dari tabel MK)
                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options(function () {
                        // Ambil semester yang unik dari tabel MK
                        return Mk::query()
                            ->select('semester')
                            ->distinct()
                            ->orderBy('semester')
                            ->pluck('semester', 'semester')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->where('semester', $data['value']);
                        }
                    })
                    ->placeholder('Pilih Semester')

            ], FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CpmksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCpmks::route('/'),
            'create' => Pages\CreateCpmk::route('/create'),
            'edit' => Pages\EditCpmk::route('/{record}/edit'),
        ];
    }
}
