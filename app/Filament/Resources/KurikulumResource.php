<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KurikulumResource\Pages;
use App\Models\Kurikulum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;

class KurikulumResource extends Resource
{
    protected static ?string $model = Kurikulum::class;

    protected static ?string $navigationLabel = 'Kurikulum';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;
    protected static ?string $breadcrumb = 'Kurikulum';
    protected static ?string $label = 'Kurikulum';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kurikulum')
                    ->label('Nama Kurikulum')
                    ->required(),

                Forms\Components\Select::make('prodi_id')
                    ->label('Program Studi')
                    ->options(function () {
                        $user = Auth::user();
                        return $user->prodis->pluck('nama_prodi', 'id')->toArray();
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kurikulum')
                    ->searchable()
                    ->label('Nama Kurikulum'),

                Tables\Columns\TextColumn::make('prodi.nama_prodi')
                    ->label('Prodi'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'tidak aktif',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('prodi_id')
                    ->label('Program Studi')
                    ->options(function () {
                        $user = Auth::user();
                        return $user->prodis->pluck('nama_prodi', 'id')->toArray();
                    })
                    ->placeholder('Pilih Program Studi')
                    ->query(function (Builder $query, array $data) {
                        return $query->where('prodi_id', $data['value']);
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Custom Action untuk toggle status
                Action::make('Aktifkan')
                    ->label('Aktifkan')
                    ->action(function (Kurikulum $record) {
                        // Set semua kurikulum di prodi ini menjadi tidak aktif
                        Kurikulum::where('prodi_id', $record->prodi_id)
                            ->update(['status' => 'tidak aktif']);

                        // Aktifkan kurikulum ini
                        $record->update(['status' => 'aktif']);
                    })
                    ->icon('heroicon-o-arrow-path') // Menggunakan ikon alternatif
                    ->requiresConfirmation()
                    ->color('primary')
                    // Hanya tampilkan jika kurikulum saat ini tidak aktif
                    ->visible(fn($record) => $record->status === 'tidak aktif'),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKurikulums::route('/'),
            'create' => Pages\CreateKurikulum::route('/create'),
            'edit' => Pages\EditKurikulum::route('/{record}/edit'),
        ];
    }
}
