<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\LihatDetailMahasiswaResource\Pages;
use App\Models\Cpmk;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LihatDetailMahasiswaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        // Ambil MK Ditawarkan ID dari request atau session
        $mkDitawarkanId = request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id');

        if (!$mkDitawarkanId) {
            \Filament\Notifications\Notification::make()
                ->title('MK Ditawarkan tidak ditemukan')
                ->warning()
                ->send();
            return $table;
        }

        // Ambil data CPMK yang terkait dengan MK Ditawarkan
        $cpmks = Cpmk::whereHas('cplMk.mkDitawarkan', function ($query) use ($mkDitawarkanId) {
            $query->where('mk_ditawarkan.id', $mkDitawarkanId);
        })->get();


        // Tambahkan kolom nilai CPMK secara dinamis
        if ($cpmks->isEmpty()) {
            $columns[] = TextColumn::make('cpmk_tidak_ada')
                ->label('CPMK')
                ->getStateUsing(fn() => 'CPMK tidak ada');
        } else {
            foreach ($cpmks as $cpmk) {
                $columns[] = TextColumn::make("nilai_{$cpmk->id}")
                    ->label("{$cpmk->kode_cpmk}")
                    ->getStateUsing(function () use ($cpmk, $mkDitawarkanId) {
                        $userId = Auth::id();
                        $cpmkMahasiswa = $cpmk->cpmkMahasiswa()
                            ->whereHas('krsMahasiswa', function ($query) use ($userId, $mkDitawarkanId) {
                                $query->where('user_id', $userId)
                                    ->where('mk_ditawarkan_id', $mkDitawarkanId);
                            })
                            ->first();

                        return $cpmkMahasiswa ? $cpmkMahasiswa->nilai : '-';
                    });
            }
        }

        return $table
            ->columns($columns)
            ->filters([])
            ->actions([]); // Tidak ada aksi tambahan
    }

    protected function getTableQuery()
    {
        // Tidak ada query tambahan karena data diambil langsung dari Auth::user()
        return User::query()
            ->where('id', Auth::id()) // Batasi data hanya untuk user login
            ->with(['krsMahasiswas.cpmkMahasiswa.cpmk']); // Include relasi yang diperlukan
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLihatDetailMahasiswas::route('/'),
        ];
    }
}
