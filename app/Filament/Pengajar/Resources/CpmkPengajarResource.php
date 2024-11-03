<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\CpmkPengajarResource\Pages;
use App\Filament\Pengajar\Resources\CpmkPengajarResource\RelationManagers;
use App\Filament\Pengajar\Resources\CpmkPengajarResource\RelationManagers\CpmksRelationManager;
use App\Models\CpmkPengajar;
use App\Models\Kurikulum;
use App\Models\Mk;
use App\Models\MkDitawarkan;
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

class CpmkPengajarResource extends Resource
{
    protected static ?string $model = MkDitawarkan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'CPMK';

    protected static ?string $breadcrumb = 'CPMK Matakuliah';

    protected static ?string $navigationLabel = 'CPMK Matakuliah';

    protected static ?string $label = 'Matakuliah Ditawarkan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
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
                            ->options(function () {
                                return \App\Models\Semester::with('tahunAjaran')
                                    ->get()
                                    ->mapWithKeys(function ($semester) {
                                        $tahunAjaran = $semester->tahunAjaran->nama_tahun_ajaran;
                                        return [$semester->id => "Semester {$semester->angka_semester} - {$tahunAjaran}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->disabled()
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
                            ->disabled()
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
                    ->sortable()
                    ->extraAttributes(['class' => 'w-20']),
                //Semester
                Tables\Columns\TextColumn::make('semester.angka_semester')
                    ->label('Semester')
                    ->extraAttributes(['class' => 'w-20']),
                //Kelas
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->extraAttributes(['class' => 'w-20']),

                Tables\Columns\TextColumn::make('pengajars')
                    ->label('Pengajar')
                    ->formatStateUsing(function ($record) {
                        // Ambil nama-nama pengajar dan tampilkan satu per baris
                        return $record->pengajars->map(fn($pengajar) => $pengajar->user->name)->implode('<br>');
                    })
                    ->html() // Mengaktifkan rendering HTML agar <br> berfungsi
                    ->extraAttributes(['class' => 'w-64']), // Lebar kolom

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
            //default sort semester
            ->defaultSort(function (Builder $query) {
                // Join dengan tabel semester dan mk untuk sorting berdasarkan angka_semester dan kode
                $query->select('mk_ditawarkan.*') // Pastikan memilih kolom dari mk_ditawarkan
                    ->join('semester', 'mk_ditawarkan.semester_id', '=', 'semester.id')
                    ->join('mk', 'mk_ditawarkan.mk_id', '=', 'mk.id')
                    ->orderBy('semester.angka_semester', 'asc') // Sort by semester
                    ->orderBy('mk.kode', 'asc');  // Sort by kode MK
            })

            ->filters([
                // Filter Prodi dan Tahun Ajaran dengan form custom
                SelectFilter::make('filter_prodi_tahun_ajaran')
                    ->label('Filter Prodi dan Tahun Ajaran')
                    ->form([
                        Grid::make(2) // Membuat grid dengan 2 kolom
                            ->schema([
                                // Dropdown untuk memilih Program Studi
                                Select::make('prodi_id')
                                    ->label('Program Studi')
                                    ->placeholder('Pilih Program Studi')
                                    ->options(function () {
                                        // Mendapatkan prodi terkait user yang login
                                        $user = Auth::user();
                                        return $user->prodis->pluck('nama_prodi', 'id')->toArray();
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        // Reset pilihan tahun ajaran saat prodi berubah
                                        $set('tahun_ajaran_id', null);
                                    }),

                                // Dropdown untuk memilih Tahun Ajaran
                                Select::make('tahun_ajaran_id')
                                    ->label('Tahun Ajaran')
                                    ->placeholder('Pilih Tahun Ajaran')
                                    ->options(function () {
                                        // Mengambil semua tahun ajaran
                                        return \App\Models\TahunAjaran::pluck('nama_tahun_ajaran', 'id')->toArray();
                                    })
                                    ->reactive(),
                            ]),
                    ])->columnSpanFull()
                    // Query untuk memfilter data berdasarkan Prodi dan Tahun Ajaran
                    ->query(function (Builder $query, array $data) {
                        // Jika prodi dan tahun ajaran belum dipilih, kembalikan query kosong
                        if (!isset($data['prodi_id']) && !isset($data['tahun_ajaran_id'])) {
                            $query->whereRaw('1 = 0'); // Ini memastikan tidak ada data yang ditampilkan
                            return;
                        }

                        // Filter berdasarkan Prodi jika dipilih
                        if (isset($data['prodi_id'])) {
                            $query->whereHas('mk.kurikulum.prodi', function (Builder $query) use ($data) {
                                $query->where('id', $data['prodi_id']);
                            });
                        }

                        // Filter berdasarkan Tahun Ajaran jika dipilih
                        if (isset($data['tahun_ajaran_id'])) {
                            $query->whereHas('semester', function (Builder $query) use ($data) {
                                $query->where('tahun_ajaran_id', $data['tahun_ajaran_id']);
                            });
                        }
                    }),
            ], FiltersLayout::AboveContent)

            ->actions([
                Tables\Actions\Action::make('lihatMahasiswa')
                    ->label('Lihat Mahasiswa')
                    ->icon('heroicon-o-eye')
                    ->action(function ($record) {
                        session(['mk_ditawarkan_id' => $record->id]); // Simpan di session
                        return redirect()->to(CpmkMahasiswaResource::getUrl('index', [
                            'mk_ditawarkan_id' => $record->id,
                        ]));
                    }),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCpmkPengajars::route('/'),
            'create' => Pages\CreateCpmkPengajar::route('/create'),
            'edit' => Pages\EditCpmkPengajar::route('/{record}/edit'),
        ];
    }
}
