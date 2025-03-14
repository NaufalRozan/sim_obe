<?php

namespace App\Filament\Pages;

use App\Models\KrsMahasiswa;
use App\Models\Mk;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PenilaianPerMahasiswa extends Page
{
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Penilaian';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Penilaian Per Mahasiswa';

    protected static string $view = 'filament.pages.penilaian-per-mahasiswa';

    public $mks;
    public $mahasiswa;
    public $cpmkList = [];
    public $nilaiTotal = [];
    public $bobotTotalCpmk = [];

    public function mount()
    {
        $user = Auth::user();
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil MK sesuai Prodi user
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['cpmks'])->get();

        // Ambil semua CPMK unik (tanpa duplikasi)
        $this->cpmkList = $this->mks->flatMap(function ($mk) {
            return $mk->cpmks->pluck('kode_cpmk');
        })->unique()->sort()->values()->toArray();

        // Hitung total bobot untuk setiap CPMK dari semua MK
        $this->bobotTotalCpmk = [];
        foreach ($this->mks as $mk) {
            foreach ($mk->cpmks as $cpmk) {
                if (!isset($this->bobotTotalCpmk[$cpmk->kode_cpmk])) {
                    $this->bobotTotalCpmk[$cpmk->kode_cpmk] = 0;
                }
                $this->bobotTotalCpmk[$cpmk->kode_cpmk] += $cpmk->bobot;
            }
        }

        // Ambil data mahasiswa yang sesuai dengan Prodi
        $this->mahasiswa = KrsMahasiswa::whereHas('user.prodis', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['user', 'cpmkMahasiswa.cpmk.mk'])
            ->get()
            ->groupBy('user.id')
            ->map(function ($grouped) {
                $firstEntry = $grouped->first();
                $nilaiCpmk = [];
                $totalCpmk = array_fill_keys($this->cpmkList, 0);
                $nilaiMk = [];

                foreach ($grouped as $mhs) {
                    foreach ($mhs->cpmkMahasiswa as $nilai) {
                        if ($nilai->cpmk && $nilai->cpmk->bobot && $nilai->cpmk->mk) {
                            $mkNama = $nilai->cpmk->mk->nama_mk;
                            $cpmkKode = strtoupper(trim($nilai->cpmk->kode_cpmk));
                            $bobot = $nilai->cpmk->bobot;
                            $nilaiAkhir = ($nilai->nilai * $bobot) / 100;

                            // Simpan nilai CPMK dalam array berdasarkan MK
                            $nilaiCpmk[$mkNama][$cpmkKode] = $nilaiAkhir;

                            // Menjumlahkan total nilai MK
                            if (!isset($nilaiMk[$mkNama])) {
                                $nilaiMk[$mkNama] = 0;
                            }
                            $nilaiMk[$mkNama] += $nilaiAkhir;

                            if (isset($totalCpmk[$cpmkKode])) {
                                $totalCpmk[$cpmkKode] += $nilaiAkhir;
                            }
                        }
                    }
                }

                return [
                    'nama' => $firstEntry->user->name,
                    'nilai' => $nilaiCpmk,
                    'total_cpmk' => $totalCpmk,
                    'nilai_mk' => $nilaiMk,
                ];
            })->values();
    }
}
