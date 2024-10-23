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
use App\Models\MkDitawarkan;
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
    protected static ?string $model = MkDitawarkan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'CPMK';

    protected static ?string $breadcrumb = 'CPMK Matakuliah';

    protected static ?string $navigationLabel = 'CPMK Matakuliah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Select::make('kurikulum_id')
                            ->label('Kurikulum')
                            ->options(function () {
                                $user = Auth::user();
                                $prodiIds = $user->prodis->pluck('id');
                                return Kurikulum::whereIn('prodi_id', $prodiIds)->pluck('nama_kurikulum', 'id');
                            })
                            ->reactive(),

                        Select::make('mk_id')
                            ->label('Matakuliah')
                            ->placeholder('Pilih Matakuliah')
                            ->options(function (callable $get) {
                                $kurikulum = Kurikulum::find($get('kurikulum_id'));

                                if ($kurikulum) {
                                    return $kurikulum->mks->pluck('nama_mk', 'id');
                                }

                                return Mk::all()->pluck('nama_mk', 'id');
                            })
                            ->disabled(fn(callable $get) => is_null($get('kurikulum_id')))
                            ->required(),

                        Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'angka_semester')
                            ->required(),

                        Select::make('kelas')
                            ->label('Kelas')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                                'E' => 'E',
                                'F' => 'F',
                            ])
                            ->required(),

                        Forms\Components\FileUpload::make('rps')
                            ->label('Upload RPS')
                            ->directory('rps-files')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(2048)
                            ->preserveFilenames()
                            ->afterStateUpdated(function ($state, callable $set, $record) {
                                if ($record && !$state && $record->rps) {
                                    Storage::delete('rps-files/' . $record->rps);
                                    $record->update(['rps' => null]);
                                }
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Matakuliah
                Tables\Columns\TextColumn::make('mk.nama_mk')
                    ->label('Mata Kuliah')
                    ->wrap()
                    ->searchable()
                    ->extraAttributes(['class' => 'w-64']),
                //kode_mk
                Tables\Columns\TextColumn::make('mk.kode')
                    ->label('Kode MK')
                    ->wrap()
                    ->searchable()
                    ->extraAttributes(['class' => 'w-20']),
                //Semester
                Tables\Columns\TextColumn::make('semester.angka_semester')
                    ->label('Semester')
                    ->extraAttributes(['class' => 'w-20']),
                //Kelas
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->extraAttributes(['class' => 'w-20']),
                // // Kolom RPS
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
                //
            ], FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
