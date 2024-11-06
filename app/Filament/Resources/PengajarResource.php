<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajarResource\Pages;
use App\Models\Pengajar;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PengajarResource extends Resource
{
    protected static ?string $model = Pengajar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengajar';

    protected static ?string $breadcrumb = 'Pengajar';

    protected static ?string $navigationLabel = 'Pengajar';

    protected static ?string $label = 'Pengajar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Dosen / Staf')
                    ->options(function () {
                        $user = Auth::user(); // Mendapatkan user yang sedang login
                        $prodiIds = $user->prodis->pluck('id'); // Ambil semua prodi user yang login

                        // Hanya menampilkan user dengan role Dosen atau Staff yang terkait dengan prodi
                        return User::whereIn('role', ['Dosen', 'Staf'])
                            ->whereHas('prodis', function (Builder $query) use ($prodiIds) {
                                $query->whereIn('prodis.id', $prodiIds); // Filter berdasarkan prodi
                            })
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengajar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.role')
                    ->label('Role')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'Dosen' => 'Dosen',
                        'Staff' => 'Staff',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('user', function ($query) use ($data) {
                                $query->where('role', $data['value']);
                            });
                        }
                    }),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajars::route('/'),
            'create' => Pages\CreatePengajar::route('/create'),
            'edit' => Pages\EditPengajar::route('/{record}/edit'),
        ];
    }
}
