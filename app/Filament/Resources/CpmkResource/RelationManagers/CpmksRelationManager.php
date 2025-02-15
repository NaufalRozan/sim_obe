<?php

namespace App\Filament\Resources\CpmkResource\RelationManagers;

use App\Models\Cpl;
use App\Models\CplHasMk;
use App\Models\Mk;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CpmksRelationManager extends RelationManager
{
    protected static string $relationship = 'cpmks';

    //nama section
    public static ?string $title = 'CPMK';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Dropdown untuk CPL terkait dengan MkDitawarkan melalui Mk
                Select::make('cpl_id')
                    ->label('Pilih CPL')
                    ->options(function () {
                        $mkId = $this->ownerRecord->mk_id; // Ambil mk_id dari ownerRecord

                        // Ambil CPL terkait melalui Mk
                        return CplHasMk::where('mk_id', $mkId)
                            ->with('cpl')
                            ->get()
                            ->mapWithKeys(function ($cplHasMk) {
                                return [
                                    $cplHasMk->cpl_id => "{$cplHasMk->cpl->nama_cpl} - {$cplHasMk->cpl->deskripsi}"
                                ];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $cplHasMk = CplHasMk::with('cpl')->where('cpl_id', $value)->first();
                        return $cplHasMk && $cplHasMk->cpl
                            ? "{$cplHasMk->cpl->nama_cpl} - {$cplHasMk->cpl->deskripsi}"
                            : '-';
                    })
                    ->default(function (callable $get, $record) {
                        if ($record) {
                            $cplHasMk = CplHasMk::find($record->cpl_mk_id);
                            return $cplHasMk ? $cplHasMk->cpl_id : null;
                        }
                        return null;
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $mkId = $this->ownerRecord->mk_id; // Ambil mk_id dari ownerRecord
                        $cplHasMk = CplHasMk::where('mk_id', $mkId)
                            ->where('cpl_id', $state)
                            ->first();

                        if ($cplHasMk) {
                            $set('cpl_mk_id', $cplHasMk->id);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        if ($record) {
                            $cplHasMk = CplHasMk::find($record->cpl_mk_id);
                            if ($cplHasMk) {
                                $set('cpl_id', $cplHasMk->cpl_id);
                            }
                        }
                    }),

                // Hidden input untuk cpl_mk_id
                Forms\Components\Hidden::make('cpl_mk_id')
                    ->required(),
                // Input untuk kode_cpmk
                Forms\Components\TextInput::make('kode_cpmk')
                    ->label('Kode CPMK')
                    ->required(),

                // Input untuk deskripsi
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->required(),

                // Input untuk bobot
                Forms\Components\TextInput::make('bobot')
                    ->label('Bobot')
                    ->maxValue(100)
                    ->numeric(),
                //batas nilai lulus
                Forms\Components\TextInput::make('batas_nilai_lulus')
                    ->label('Batas Nilai Lulus')
                    ->maxValue(100)
                    ->numeric(),
                //batas nilai memuaskan
                Forms\Components\TextInput::make('batas_nilai_memuaskan')
                    ->label('Batas Nilai Memuaskan')
                    ->maxValue(100)
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_cpmk')
            ->columns([
                Tables\Columns\TextColumn::make('cplMk.cpl.nama_cpl')
                    ->label('Kode CPL')
                    ->formatStateUsing(function ($record) {
                        return $record->cplMk && $record->cplMk->cpl
                            ? "{$record->cplMk->cpl->nama_cpl} - {$record->cplMk->cpl->deskripsi}"
                            : '-';
                    })
                    ->sortable()
                    ->color(function ($record) {
                        // Hitung total bobot semua CPMK berdasarkan MK yang sama
                        $totalBobot = \App\Models\Cpmk::whereHas('cplMk', function ($query) use ($record) {
                            $query->where('mk_id', $record->cplMk->mk_id); // Batasi berdasarkan MK yang sama
                        })->sum('bobot');

                        // Jika total bobot > 100, beri warna merah
                        return $totalBobot > 100 ? 'danger' : null;
                    })
                    ->wrap()
                    ->extraAttributes(['class' => 'w-64']),

                Tables\Columns\TextColumn::make('kode_cpmk')
                    ->label('Kode CPMK')
                    ->sortable()
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::whereHas('cplMk', function ($query) use ($record) {
                            $query->where('mk_id', $record->cplMk->mk_id);
                        })->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    })
                    ->wrap()
                    ->extraAttributes(['class' => 'w-32']),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::whereHas('cplMk', function ($query) use ($record) {
                            $query->where('mk_id', $record->cplMk->mk_id);
                        })->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    })
                    ->wrap()
                    ->extraAttributes(['class' => 'w-96']),

                Tables\Columns\TextColumn::make('bobot')
                    ->label('Bobot')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::whereHas('cplMk', function ($query) use ($record) {
                            $query->where('mk_id', $record->cplMk->mk_id);
                        })->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    })
                    ->extraAttributes(['class' => 'w-16']),

                Tables\Columns\TextColumn::make('batas_nilai_lulus')
                    ->label('Batas Nilai Lulus')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::whereHas('cplMk', function ($query) use ($record) {
                            $query->where('mk_id', $record->cplMk->mk_id);
                        })->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    })
                    ->extraAttributes(['class' => 'w-16']),

                Tables\Columns\TextColumn::make('batas_nilai_memuaskan')
                    ->label('Batas Nilai Memuaskan')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::whereHas('cplMk', function ($query) use ($record) {
                            $query->where('mk_id', $record->cplMk->mk_id);
                        })->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    })
                    ->extraAttributes(['class' => 'w-16']),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
