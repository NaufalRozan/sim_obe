<?php

namespace App\Livewire;

use App\Models\KrsMahasiswa;
use App\Models\Mk;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PenilaianMahasiswa extends Component
{
    public $angkatan;
    public $angkatanList = [];
    public $mks;
    public $mahasiswa;
    public $cpmkList = [];
    public $cplList = [];
    public $bobotTotalCpmk = [];
    public $bobotTotalCpl = [];


    public function mount()
    {
        $user = Auth::user();
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil semua angkatan yang tersedia
        $this->angkatanList = KrsMahasiswa::with('user')
            ->whereHas('user', function ($query) {
                $query->whereNotNull('angkatan');
            })
            ->get()
            ->pluck('user.angkatan')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Tambahkan opsi "Semua Angkatan"
        array_unshift($this->angkatanList, 'Semua Angkatan');

        // Set default angkatan ke "Semua Angkatan"
        $this->angkatan = 'Semua Angkatan';

        // Ambil data MK dan mahasiswa
        $this->loadData();
    }

    #[On('update-angkatan')] // Livewire v3 event binding
    public function updatedAngkatan()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $user = Auth::user();
        $prodiIds = $user->prodis->pluck('id')->toArray();

        // Ambil MK sesuai Prodi user
        $this->mks = Mk::whereHas('kurikulum', function ($query) use ($prodiIds) {
            $query->whereIn('prodi_id', $prodiIds);
        })->with(['cpmks.cplMk.cpl'])->get();

        // Ambil semua CPMK unik
        $this->cpmkList = $this->mks->flatMap(function ($mk) {
            return $mk->cpmks->pluck('kode_cpmk');
        })->unique()->sort()->values()->toArray();

        // Ambil semua CPL unik dari semua MK tanpa duplikasi
        $this->cplList = $this->mks->flatMap(function ($mk) {
            return $mk->cpmks->pluck('cplMk.cpl.nama_cpl')->unique();
        })->filter()->unique()->sort()->values()->toArray();

        // Hitung total bobot untuk setiap CPMK
        $this->bobotTotalCpmk = [];
        foreach ($this->mks as $mk) {
            foreach ($mk->cpmks as $cpmk) {
                if (!isset($this->bobotTotalCpmk[$cpmk->kode_cpmk])) {
                    $this->bobotTotalCpmk[$cpmk->kode_cpmk] = 0;
                }
                $this->bobotTotalCpmk[$cpmk->kode_cpmk] += $cpmk->bobot;
            }
        }

        // Hitung total bobot untuk setiap CPL dari semua CPMK
        $this->bobotTotalCpl = [];
        foreach ($this->mks as $mk) {
            foreach ($mk->cpmks as $cpmk) {
                if ($cpmk->cplMk && $cpmk->cplMk->cpl) {
                    $cplNama = $cpmk->cplMk->cpl->nama_cpl;
                    if (!isset($this->bobotTotalCpl[$cplNama])) {
                        $this->bobotTotalCpl[$cplNama] = 0;
                    }
                    $this->bobotTotalCpl[$cplNama] += $cpmk->bobot;
                }
            }
        }

        // ✅ Hitung jumlah CPL dalam MK berdasarkan struktur MK, bukan dari nilai mahasiswa
        $jumlahCplDalamMk = [];
        foreach ($this->mks as $mk) {
            $mkNama = $mk->nama_mk;
            foreach ($mk->cpmks as $cpmk) {
                if ($cpmk->cplMk && $cpmk->cplMk->cpl) {
                    $cplNama = $cpmk->cplMk->cpl->nama_cpl;
                    if (!isset($jumlahCplDalamMk[$mkNama][$cplNama])) {
                        $jumlahCplDalamMk[$mkNama][$cplNama] = 0;
                    }
                    $jumlahCplDalamMk[$mkNama][$cplNama]++;
                }
            }
        }

        // Ambil data mahasiswa berdasarkan angkatan yang dipilih
        $this->mahasiswa = KrsMahasiswa::whereHas('user', function ($query) {
            if ($this->angkatan !== 'Semua Angkatan') {
                $query->where('angkatan', $this->angkatan);
            }
        })
            ->whereHas('user.prodis', function ($query) use ($prodiIds) {
                $query->whereIn('prodi_id', $prodiIds);
            })
            ->with(['user', 'cpmkMahasiswa.cpmk.cplMk.cpl'])
            ->get()
            ->groupBy('user.id')
            ->map(function ($grouped) use ($jumlahCplDalamMk) {
                $firstEntry = $grouped->first();
                $nilaiCpmk = [];
                $nilaiCpl = [];
                $nilaiCplTidakFull = [];
                $nilaiMk = [];

                $cplPerMk = [];

                foreach ($grouped as $mhs) {
                    foreach ($mhs->cpmkMahasiswa as $nilai) {
                        if (!$nilai->cpmk || !$nilai->cpmk->cplMk || !$nilai->cpmk->cplMk->cpl) continue;

                        $mkNama = $nilai->cpmk->mk->nama_mk;
                        $cpmkKode = strtoupper(trim($nilai->cpmk->kode_cpmk));
                        $cplNama = $nilai->cpmk->cplMk->cpl->nama_cpl;
                        $bobot = $nilai->cpmk->bobot;
                        $nilaiAkhir = $nilai->nilai !== null ? ($nilai->nilai * $bobot) / 100 : null;

                        // Simpan nilai CPMK
                        $nilaiCpmk[$mkNama][$cpmkKode] = $nilaiAkhir;

                        // Simpan nilai CPL total (hanya yang ada nilai)
                        if (!isset($nilaiCpl[$cplNama])) {
                            $nilaiCpl[$cplNama] = 0;
                        }
                        $nilaiCpl[$cplNama] += $nilaiAkhir ?? 0;

                        // Simpan nilai MK
                        if (!isset($nilaiMk[$mkNama])) {
                            $nilaiMk[$mkNama] = 0;
                        }
                        $nilaiMk[$mkNama] += $nilaiAkhir ?? 0;

                        // Simpan nilai untuk CPL Tidak Full
                        if (!isset($cplPerMk[$mkNama][$cplNama])) {
                            $cplPerMk[$mkNama][$cplNama] = [];
                        }
                        $cplPerMk[$mkNama][$cplNama][] = $nilaiAkhir;
                    }
                }

                // ✅ Perhitungan Capaian CPL Tidak Full
                foreach ($jumlahCplDalamMk as $mkNama => $cpls) {
                    foreach ($cpls as $cplNama => $jumlahCplSamaDalamMk) {
                        $nilaiCpmks = $cplPerMk[$mkNama][$cplNama] ?? [];
                        $nilaiTotal = 0;
                        $hasKosong = false;

                        // hitung total nilai, tandai jika ada yg null
                        for ($i = 0; $i < $jumlahCplSamaDalamMk; $i++) {
                            if (!isset($nilaiCpmks[$i]) || $nilaiCpmks[$i] === null) {
                                $hasKosong = true;
                            } else {
                                $nilaiTotal += $nilaiCpmks[$i];
                            }
                        }

                        // jika ada kosong, bagi dengan jumlah CPL (bukan jumlah nilai yang ada)
                        $nilaiPerCpl = $hasKosong ? ($nilaiTotal / $jumlahCplSamaDalamMk) : $nilaiTotal;

                        if (!isset($nilaiCplTidakFull[$cplNama])) {
                            $nilaiCplTidakFull[$cplNama] = 0;
                        }
                        $nilaiCplTidakFull[$cplNama] += $nilaiPerCpl;
                    }
                }

                return [
                    'nama' => $firstEntry->user->name,
                    'nilai_cpmk' => $nilaiCpmk,
                    'nilai_cpl' => $nilaiCpl,
                    'nilai_cpl_tidak_full' => $nilaiCplTidakFull,
                    'nilai_mk' => $nilaiMk,
                ];
            });
    }



    public function render()
    {
        return view('livewire.penilaian-mahasiswa');
    }
}
