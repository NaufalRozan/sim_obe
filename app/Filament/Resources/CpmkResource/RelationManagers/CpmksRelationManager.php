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
                // Dropdown untuk CPL, menampilkan CPL terkait dengan MK yang dipilih di CpmkResource
                Select::make('cpl_id')  // Menggunakan 'cpl_id' untuk dropdown CPL
                    ->label('Pilih CPL')
                    ->options(function (callable $get) {
                        // Ambil MK yang dipilih
                        $mkId = $this->ownerRecord->id;

                        // Hanya tampilkan CPL yang terkait dengan MK yang dipilih
                        return CplHasMk::where('mk_id', $mkId)
                            ->with('cpl')
                            ->get()
                            ->pluck('cpl.nama_cpl', 'cpl_id') // Mengambil nama CPL untuk ditampilkan
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value) {
                        // Menampilkan kembali CPL yang tersimpan
                        $cplHasMk = CplHasMk::with('cpl')->where('cpl_id', $value)->first();
                        return $cplHasMk && $cplHasMk->cpl ? $cplHasMk->cpl->nama_cpl : '-';
                    })
                    ->default(function (callable $get, $record) {
                        // Jika sedang mengedit, ambil nilai cpl_mk_id yang tersimpan di record dan tampilkan di dropdown
                        if ($record) {
                            $cplHasMk = CplHasMk::find($record->cpl_mk_id); // Ambil dari cpl_mk_id yang tersimpan
                            return $cplHasMk ? $cplHasMk->cpl_id : null;
                        }
                        return null;
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Setelah CPL dipilih, ambil cpl_mk_id dari pivot cpl_mk
                        $mkId = $this->ownerRecord->id;
                        $cplHasMk = CplHasMk::where('mk_id', $mkId)
                            ->where('cpl_id', $state)
                            ->first();
                        if ($cplHasMk) {
                            // Simpan cpl_mk_id yang ditemukan dari pivot table ke form
                            $set('cpl_mk_id', $cplHasMk->id);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        // Saat form di-load ulang (edit), pastikan CPL yang tersimpan di-load ulang dari cpl_mk_id
                        if ($record) {
                            $cplHasMk = CplHasMk::find($record->cpl_mk_id); // Ambil dari cpl_mk_id
                            if ($cplHasMk) {
                                $set('cpl_id', $cplHasMk->cpl_id); // Set dropdown dengan cpl_id yang benar
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
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_cpmk')
            ->columns([
                Tables\Columns\TextColumn::make('cplMk.cpl.nama_cpl')
                    ->label('Nama CPL')
                    ->formatStateUsing(function ($record) {
                        return $record->cplMk && $record->cplMk->cpl ? $record->cplMk->cpl->nama_cpl : '-';
                    })
                    ->sortable()
                    ->color(function ($record) {
                        // Ambil total bobot dari tabel `cpmk` untuk `cpl_mk_id` yang sama
                        $totalBobot = \App\Models\Cpmk::where('cpl_mk_id', $record->cpl_mk_id)->sum('bobot');

                        // Jika total bobot > 100, beri warna teks merah
                        return $totalBobot > 100 ? 'danger' : null;
                    }),

                Tables\Columns\TextColumn::make('kode_cpmk')
                    ->label('Kode CPMK')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::where('cpl_mk_id', $record->cpl_mk_id)->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    }),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::where('cpl_mk_id', $record->cpl_mk_id)->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    }),

                Tables\Columns\TextColumn::make('bobot')
                    ->label('Bobot')
                    ->color(function ($record) {
                        $totalBobot = \App\Models\Cpmk::where('cpl_mk_id', $record->cpl_mk_id)->sum('bobot');
                        return $totalBobot > 100 ? 'danger' : null;
                    }),
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
