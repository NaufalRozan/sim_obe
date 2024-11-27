<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cpl;
use App\Models\CpmkMahasiswa;
use Illuminate\Support\Facades\Log;

class LihatLaporanMahasiswaTable extends Component
{
    public $krs_mahasiswa_id;
    public $mk_ditawarkan_id;
    public $cpl_id = null;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function mount($krs_mahasiswa_id, $mk_ditawarkan_id, $cpl_id = null)
    {
        $this->krs_mahasiswa_id = $krs_mahasiswa_id;
        $this->mk_ditawarkan_id = $mk_ditawarkan_id;
        $this->cpl_id = $cpl_id;
    }

    public function updatedCplId()
    {
        Log::info('CPL ID diperbarui:', ['cpl_id' => $this->cpl_id]);
        $this->emitSelf('refreshTable');
    }

    public function getCpmkData()
    {
        $categories = [
            'Lulus Memuaskan' => collect([]),
            'Lulus' => collect([]),
            'Tidak Lulus' => collect([]),
            'Belum Diambil' => collect([]),
        ];

        $query = CpmkMahasiswa::query()
            ->where('krs_mahasiswa_id', $this->krs_mahasiswa_id)
            ->whereHas('cpmk.cplMk', function ($query) {
                $query->whereHas('mkDitawarkan', function ($query) {
                    $query->where('id', $this->mk_ditawarkan_id);
                });
            });

        if (!empty($this->cpl_id)) {
            $query->whereHas('cpmk.cplMk', function ($query) {
                $query->where('cpl_id', $this->cpl_id);
            });
        }

        $data = $query->get()
            ->map(function ($record) {
                $category = 'Belum Diambil';
                if ($record->nilai >= $record->cpmk->batas_nilai_memuaskan) {
                    $category = 'Lulus Memuaskan';
                } elseif ($record->nilai >= $record->cpmk->batas_nilai_lulus) {
                    $category = 'Lulus';
                } elseif ($record->nilai < $record->cpmk->batas_nilai_lulus) {
                    $category = 'Tidak Lulus';
                }

                return [
                    'category' => $category,
                    'kode_cpmk' => $record->cpmk->kode_cpmk,
                    'nilai' => $record->nilai ?? 'Belum Diambil',
                ];
            })
            ->groupBy('category');

        return collect($categories)->merge($data);
    }

    public function getCplOptions()
    {
        return Cpl::whereHas('mks', function ($query) {
            $query->whereHas('mkDitawarkan', function ($query) {
                $query->where('id', $this->mk_ditawarkan_id);
            });
        })->pluck('nama_cpl', 'id');
    }

    public function render()
    {
        Log::info('Rendering ulang dengan CPL ID:', ['cpl_id' => $this->cpl_id]);

        return view('livewire.lihat-laporan-mahasiswa-table', [
            'cpmkData' => $this->getCpmkData(),
            'cplOptions' => $this->getCplOptions(),
        ]);
    }
}
