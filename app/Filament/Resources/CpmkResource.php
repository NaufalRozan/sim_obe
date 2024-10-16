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
use Illuminate\Support\Facades\Storage;

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
                Forms\Components\FileUpload::make('rps')
                    ->label('Upload RPS')
                    ->directory('rps-files') // Direktori penyimpanan
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']) // Hanya file PDF dan DOC/DOCX yang diterima
                    ->maxSize(2048) // Maksimum 2MB
                    ->preserveFilenames() // Agar filename asli tetap dipertahankan
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        // Jika file RPS dihapus (state menjadi null), hapus file dari storage
                        if ($record && !$state && $record->rps) {
                            Storage::delete('rps-files/' . $record->rps); // Hapus file dari storage
                            $record->update(['rps' => null]); // Set kolom 'rps' menjadi null di database
                        }
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //semester
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->extraAttributes(['class' => 'w-20']),
                //kode_mk
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode MK')
                    ->extraAttributes(['class' => 'w-20']),
                //nama_mk
                Tables\Columns\TextColumn::make('nama_mk')
                    ->label('Nama MK')
                    ->wrap()
                    ->extraAttributes(['class' => 'w-64']),
                // Kolom RPS
                Tables\Columns\TextColumn::make('rps')
                    ->label('RPS')
                    ->formatStateUsing(function ($record) {
                        if ($record->rps) {
                            $fileName = basename($record->rps); // Mengambil nama file dari path
                            $downloadUrl = asset('storage/' . $record->rps); // Membuat URL unduhan
                            return '<a href="' . $downloadUrl . '" target="_blank" style="color: blue; text-decoration: underline;">' . $fileName . '</a>'; // Link dengan warna biru dan garis bawah
                        }
                        return 'No RPS Uploaded'; // Pesan jika tidak ada file
                    })
                    ->html() // Mengaktifkan rendering HTML
                    ->extraAttributes(['style' => 'width: 25%;']), // Lebar 25%
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
