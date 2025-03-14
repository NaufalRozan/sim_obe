<x-filament-panels::page>
    <div class="p-6 space-y-4">
        <h2 class="text-xl font-bold text-center">Penilaian Per Mahasiswa</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-black bg-white text-sm text-left">
                <thead>
                    <tr class="border-b border-black">
                        <th rowspan="2" class="px-4 py-2 border border-black text-center">No</th>
                        <th rowspan="2" class="px-4 py-2 border border-black text-center">Nama Mahasiswa</th>

                        @foreach ($mks as $mk)
                            <th colspan="{{ $mk->cpmks->count() + 1 }}"
                                class="px-4 py-2 border border-black text-center">
                                {{ $mk->kode }} - {{ $mk->nama_mk }}
                            </th>
                        @endforeach

                        <!-- Header untuk CPMK unik -->
                        @foreach ($cpmkList as $cpmk)
                            <th rowspan="2" class="px-4 py-2 border border-black text-center">Nilai {{ $cpmk }}</th>
                            <th rowspan="2" class="px-4 py-2 border border-black text-center">Capaian {{ $cpmk }}</th>
                        @endforeach
                    </tr>

                    <tr class="border-b border-black">
                        @foreach ($mks as $mk)
                            @foreach ($mk->cpmks as $cpmk)
                                <th class="px-4 py-2 border border-black text-center">{{ $cpmk->kode_cpmk }}</th>
                            @endforeach
                            <th class="px-4 py-2 border border-black text-center">Nilai MK {{ $mk->kode }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    <!-- Row untuk Total Nilai -->
                    <tr class="border-b border-black bg-gray-200 font-bold">
                        <td colspan="2" class="px-4 py-2 border border-black text-center">Total Nilai</td>

                        @foreach ($mks as $mk)
                            @php
                                $totalBobotMk = 0;
                            @endphp
                            @foreach ($mk->cpmks as $cpmk)
                                <td class="px-4 py-2 border border-black text-center">
                                    {{ $cpmk->bobot ?? '-' }}
                                </td>
                                @php
                                    $totalBobotMk += $cpmk->bobot;
                                @endphp
                            @endforeach
                            <!-- Nilai MK dijumlahkan dari total bobot CPMK -->
                            <td class="px-4 py-2 border border-black text-center font-bold">
                                {{ $totalBobotMk > 0 ? number_format($totalBobotMk, 2) : '-' }}
                            </td>
                        @endforeach

                        <!-- Total CPMK diambil dari jumlah bobot CPMK pada semua MK -->
                        @foreach ($cpmkList as $cpmk)
                            @php
                                $totalCpmkKeseluruhan = $bobotTotalCpmk[$cpmk] ?? 0;
                            @endphp
                            <td class="px-4 py-2 border border-black text-center bg-gray-300">
                                {{ $totalCpmkKeseluruhan > 0 ? number_format($totalCpmkKeseluruhan, 2) : '-' }}
                            </td>
                            <td class="px-4 py-2 border border-black text-center bg-gray-300">
                                100%
                            </td>
                        @endforeach
                    </tr>

                    <!-- Row untuk Mahasiswa -->
                    @foreach ($mahasiswa as $index => $mhs)
                        <tr class="border-b border-black">
                            <td class="px-4 py-2 border border-black text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border border-black text-center">{{ $mhs['nama'] }}</td>

                            <!-- Nilai CPMK Per Mata Kuliah -->
                            @foreach ($mks as $mk)
                                @foreach ($mk->cpmks as $cpmk)
                                    <td class="px-4 py-2 border border-black text-center">
                                        {{ isset($mhs['nilai'][$mk->nama_mk][$cpmk->kode_cpmk])
                                            ? number_format($mhs['nilai'][$mk->nama_mk][$cpmk->kode_cpmk], 2)
                                            : '-' }}
                                    </td>
                                @endforeach
                                <!-- Menampilkan total nilai MK -->
                                <td class="px-4 py-2 border border-black text-center font-bold">
                                    {{ isset($mhs['nilai_mk'][$mk->nama_mk])
                                        ? number_format($mhs['nilai_mk'][$mk->nama_mk], 2)
                                        : '-' }}
                                </td>
                            @endforeach

                            <!-- Nilai & Capaian CPMK Per CPMK Unik -->
                            @foreach ($cpmkList as $cpmk)
                                @php
                                    $totalCpmkMahasiswa = $mhs['total_cpmk'][$cpmk] ?? 0;
                                    $totalCpmkKeseluruhan = $bobotTotalCpmk[$cpmk] ?? 1;

                                    // Menghindari pembagian dengan nol
                                    $persentaseCpmkMahasiswa = $totalCpmkKeseluruhan > 0
                                        ? round(($totalCpmkMahasiswa / $totalCpmkKeseluruhan) * 100, 2) . '%'
                                        : '0%';
                                @endphp
                                <td class="px-4 py-2 border border-black text-center">
                                    {{ $totalCpmkMahasiswa > 0 ? number_format($totalCpmkMahasiswa, 2) : '-' }}
                                </td>
                                <td class="px-4 py-2 border border-black text-center">
                                    {{ $totalCpmkMahasiswa > 0 ? $persentaseCpmkMahasiswa : '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
