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

        // Ambil semua CPL unik dari CPMK
        $this->cplList = $this->mks->flatMap(function ($mk) {
            return $mk->cpmks->pluck('cplMk.cpl.nama_cpl')->unique();
        })->filter()->sort()->values()->toArray();

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
            ->map(function ($grouped) {
                $firstEntry = $grouped->first();
                $nilaiCpmk = [];
                $nilaiCpl = [];
                $nilaiCplTidakFull = [];
                $nilaiMk = [];

                foreach ($grouped as $mhs) {
                    $cplPerMk = [];

                    foreach ($mhs->cpmkMahasiswa as $nilai) {
                        if ($nilai->cpmk && $nilai->cpmk->bobot && $nilai->cpmk->cplMk && $nilai->cpmk->cplMk->cpl) {
                            $mkNama = $nilai->cpmk->mk->nama_mk;
                            $cpmkKode = strtoupper(trim($nilai->cpmk->kode_cpmk));
                            $cplNama = $nilai->cpmk->cplMk->cpl->nama_cpl;
                            $bobot = $nilai->cpmk->bobot;
                            $nilaiAkhir = ($nilai->nilai * $bobot) / 100;

                            // Simpan nilai CPMK
                            $nilaiCpmk[$mkNama][$cpmkKode] = $nilaiAkhir;

                            // Simpan nilai CPL berdasarkan CPMK yang terkait
                            if (!isset($nilaiCpl[$cplNama])) {
                                $nilaiCpl[$cplNama] = 0;
                            }
                            $nilaiCpl[$cplNama] += $nilaiAkhir;

                            // Simpan nilai MK
                            if (!isset($nilaiMk[$mkNama])) {
                                $nilaiMk[$mkNama] = 0;
                            }
                            $nilaiMk[$mkNama] += $nilaiAkhir;

                            // Kelompokkan CPL berdasarkan MK
                            $cplPerMk[$mkNama][$cplNama][] = $nilaiAkhir;
                        }
                    }

                    // Hitung "Capaian CPL Tidak Full"
                    foreach ($cplPerMk as $mkNama => $cpls) {
                        foreach ($cpls as $cplNama => $nilaiCpmks) {
                            $jumlahCpmkDalamMk = count($nilaiCpmks);
                            $nilaiPerCpmk = array_sum($nilaiCpmks) / $jumlahCpmkDalamMk;

                            if (!isset($nilaiCplTidakFull[$cplNama])) {
                                $nilaiCplTidakFull[$cplNama] = 0;
                            }
                            $nilaiCplTidakFull[$cplNama] += $nilaiPerCpmk;
                        }
                    }
                }

                return [
                    'nama' => $firstEntry->user->name,
                    'nilai_cpmk' => $nilaiCpmk,
                    'nilai_cpl' => $nilaiCpl,
                    'nilai_cpl_tidak_full' => $nilaiCplTidakFull,
                    'nilai_mk' => $nilaiMk,
                ];
            })->values();
    }


    public function render()
    {
        return view('livewire.penilaian-mahasiswa');
    }
}
