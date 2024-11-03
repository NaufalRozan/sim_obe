<?php

namespace App\Exports;

use App\Models\Cpmk;
use App\Models\KrsMahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CpmkMahasiswaTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    protected $mkDitawarkanId;
    protected $cpmks;

    public function __construct($mkDitawarkanId)
    {
        $this->mkDitawarkanId = $mkDitawarkanId;
        // Ambil semua CPMK yang terkait dengan MK yang dipilih
        $this->cpmks = Cpmk::whereHas('cplMk', function ($query) use ($mkDitawarkanId) {
            $query->whereHas('mkDitawarkan', function ($subQuery) use ($mkDitawarkanId) {
                $subQuery->where('mk_ditawarkan.id', $mkDitawarkanId);
            });
        })->get();
    }

    public function collection()
    {
        // Dapatkan mahasiswa yang mengambil MK yang ditawarkan
        return KrsMahasiswa::with('user', 'cpmkMahasiswa')
            ->where('mk_ditawarkan_id', $this->mkDitawarkanId)
            ->get();
    }

    public function headings(): array
    {
        // Headings kolom
        $headings = ['Nama Mahasiswa', 'NIM'];

        // Tambahkan heading untuk tiap CPMK
        foreach ($this->cpmks as $cpmk) {
            $headings[] = "Nilai CPMK {$cpmk->kode_cpmk}";
        }

        return $headings;
    }

    public function map($krsMahasiswa): array
    {
        // Mapping data mahasiswa
        $row = [
            $krsMahasiswa->user->name,
            $krsMahasiswa->user->nim,
        ];

        // Tambahkan nilai untuk tiap CPMK
        foreach ($this->cpmks as $cpmk) {
            $cpmkMahasiswa = $krsMahasiswa->cpmkMahasiswa->firstWhere('cpmk_id', $cpmk->id);
            $row[] = $cpmkMahasiswa ? $cpmkMahasiswa->nilai : '-';
        }

        return $row;
    }
}
