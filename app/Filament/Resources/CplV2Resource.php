<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CplV2Resource\Pages;
use App\Filament\Resources\CplV2Resource\RelationManagers;
use App\Filament\Resources\CplV2Resource\RelationManagers\CplsRelationManager;
use App\Models\Kurikulum;
use App\Models\Mk;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CplV2Resource extends Resource
{
    protected static ?string $model = Mk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'CPL';

    protected static ?string $breadcrumb = 'CPL Prodi V2';

    protected static ?string $navigationLabel = 'CPL Prodi V2';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // kode
                TextInput::make('kode')
                    ->label('Kode')
                    ->required()
                    ->disabled()
                    ->placeholder('Masukkan Kode Mata Kuliah'),

                // nama_mk
                TextInput::make('nama_mk')
                    ->label('Nama Mata Kuliah')
                    ->required()
                    ->disabled()
                    ->placeholder('Masukkan Nama Mata Kuliah'),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->query(Mk::with('cpls')) // Memuat relasi CPL
            ->columns([
                // Kolom dasar seperti semester, kode, dan nama mata kuliah
                TextColumn::make('semester')
                    ->label('Semester'),
                TextColumn::make('kode')
                    ->label('Kode'),
                TextColumn::make('nama_mk')
                    ->label('Mata Kuliah'),
                // Ambil MK pertama untuk mengetahui jumlah CPL yang dimiliki
                ...collect(Mk::with('cpls')->first()?->cpls ?? [])->map(function ($cpl, $index) {
                    return TextColumn::make('cpls.' . $index . '.pivot.bobot')
                        ->label(($cpl->nama_cpl ?? ''));
                })->toArray()
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
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CplsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCplV2S::route('/'),
            // 'create' => Pages\CreateCplV2::route('/create'),
            'edit' => Pages\EditCplV2::route('/{record}/edit'),
        ];
    }
}
