<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'User';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Nama
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                // Email
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                //form password jika create user password wajib di isi, jika edit data maka password tidak wajib di isi
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->visible(fn(callable $get) => is_null($get('id')))
                    ->required(fn(callable $get) => is_null($get('id'))),

                // Role
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'Prodi' => 'Prodi',
                        'Dosen' => 'Dosen',
                        'Staf' => 'Staf',
                        'Mahasiswa' => 'Mahasiswa',
                    ])
                    ->reactive()
                    ->required(),

                // NIM
                Forms\Components\TextInput::make('nim')
                    ->label('NIM')
                    ->visible(fn(callable $get) => $get('role') === 'Mahasiswa')
                    ->required(fn(callable $get) => $get('role') === 'Mahasiswa'),

                // NIP
                Forms\Components\TextInput::make('nip')
                    ->label('NIP')
                    ->visible(fn(callable $get) => in_array($get('role'), ['Dosen', 'Staf']))
                    ->required(fn(callable $get) => in_array($get('role'), ['Dosen', 'Staf'])),

                // Pilih Prodi
                Forms\Components\MultiSelect::make('prodis')
                    ->label('Program Studi')
                    ->relationship('prodis', 'nama_prodi') // Menghubungkan dengan relasi 'prodis'
                    ->preload()
                    ->required(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // nama
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),
                // email
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                // role
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
                    ->label('Role'),
                // nim
                Tables\Columns\TextColumn::make('nim')
                    ->searchable()
                    ->label('NIM'),
                // nip
                Tables\Columns\TextColumn::make('nip')
                    ->searchable()
                    ->label('NIP'),
                //prodi

            ])
            ->filters([
                // role
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'Prodi' => 'Prodi',
                        'Dosen' => 'Dosen',
                        'Staf' => 'Staf',
                        'Mahasiswa' => 'Mahasiswa',
                    ]),
            ], layout: FiltersLayout::AboveContent)
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
