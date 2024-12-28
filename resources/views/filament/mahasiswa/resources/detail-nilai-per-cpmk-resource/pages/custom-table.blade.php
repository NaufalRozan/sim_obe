<x-filament-panels::page>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-6 text-gray-700">Detail Nilai Mahasiswa: {{ $mahasiswa->name }}</h1>
        <div class="overflow-hidden rounded-lg shadow border border-gray-300">
            <table class="w-full divide-y divide-gray-200 bg-white border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                            Kategori
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Daftar CPMK
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $categories = [
                            'Lulus Memuaskan' => [],
                            'Lulus' => [],
                            'Tidak Lulus' => [],
                            'Belum Diambil' => [],
                        ];

                        // Kategori Belum Diambil
                        $takenMks = $mahasiswa
                            ->krsMahasiswas()
                            ->with(['mkDitawarkan', 'cpmkMahasiswa.cpmk.cplMk.mk'])
                            ->get();

                        $takenMkIds = $takenMks->pluck('mkDitawarkan.mk_id')->unique()->toArray();

                        $prodiIds = $mahasiswa->prodis->pluck('id');

                        $allMks = \App\Models\Mk::whereHas('kurikulum.prodi', function ($query) use ($prodiIds) {
                            $query->whereIn('id', $prodiIds);
                        })
                            ->with(['cpmks'])
                            ->get();

                        foreach ($allMks as $mk) {
                            $isTaken = in_array($mk->id, $takenMkIds);

                            if ($isTaken) {
                                $cpmksBelumDinilai = $takenMks->filter(function ($krs) use ($mk) {
                                    return $krs->mkDitawarkan->mk_id === $mk->id &&
                                        $krs->cpmkMahasiswa->every(function ($cpmkMahasiswa) {
                                            return is_null($cpmkMahasiswa->nilai);
                                        });
                                });

                                if ($cpmksBelumDinilai->isNotEmpty()) {
                                    foreach ($mk->cpmks as $cpmk) {
                                        $categories['Belum Diambil'][] = ['cpmk' => $cpmk, 'mk' => $mk];
                                    }
                                }
                            } else {
                                foreach ($mk->cpmks as $cpmk) {
                                    $categories['Belum Diambil'][] = ['cpmk' => $cpmk, 'mk' => $mk];
                                }
                            }
                        }

                        foreach ($mahasiswa->krsMahasiswas as $krs) {
                            foreach ($krs->cpmkMahasiswa as $cpmkMahasiswa) {
                                $nilai = $cpmkMahasiswa->nilai;
                                $cpmk = $cpmkMahasiswa->cpmk;
                                $mk = $cpmk->cplMk->mk;

                                if ($nilai >= $cpmk->batas_nilai_memuaskan) {
                                    $categories['Lulus Memuaskan'][] = ['cpmk' => $cpmk, 'mk' => $mk];
                                } elseif ($nilai >= $cpmk->batas_nilai_lulus) {
                                    $categories['Lulus'][] = ['cpmk' => $cpmk, 'mk' => $mk];
                                } elseif (!is_null($nilai)) {
                                    $categories['Tidak Lulus'][] = ['cpmk' => $cpmk, 'mk' => $mk];
                                }
                            }
                        }
                    @endphp

                    @foreach ($categories as $category => $cpmks)
                        <tr>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-left align-top border-r border-gray-300">
                                {{ $category }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if (count($cpmks) > 0)
                                    <div class="space-y-1">
                                        @foreach ($cpmks as $data)
                                            <div>
                                                {{ $data['mk']->kode }} - {{ $data['cpmk']->kode_cpmk }} -
                                                {{ $data['cpmk']->deskripsi }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <em class="text-gray-500">Belum ada data</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
