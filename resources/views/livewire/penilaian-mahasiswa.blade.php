<div>
    <!-- Filter Angkatan -->
    <div class="mb-4">
        <label for="angkatan" class="block text-sm font-semibold text-gray-700">Filter Angkatan:</label>
        <div class="relative w-64">
            <select id="angkatan" wire:model.live="angkatan"
                class="block w-full mt-1 pl-4 pr-10 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white appearance-none">
                @foreach ($angkatanList as $tahun)
                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="overflow-x-auto">
        <table class="w-full border border-black bg-white text-sm text-left">
            <thead>
                <tr class="border-b border-black">
                    <th rowspan="3" class="px-4 py-2 border border-black text-center">No</th>
                    <th rowspan="3" class="px-4 py-2 border border-black text-center">Nama Mahasiswa</th>

                    @foreach ($mks as $mk)
                        <th colspan="{{ $mk->cpmks->count() }}" class="px-4 py-2 border border-black text-center">
                            {{ $mk->kode }} - {{ $mk->nama_mk }}
                        </th>
                        <th rowspan="3" class="px-4 py-2 border border-black text-center">Nilai MK
                            {{ $mk->kode }}</th>
                    @endforeach

                    <!-- Pastikan CPL hanya muncul sekali -->
                    @foreach ($cplList as $cpl)
                        <th rowspan="3" class="px-4 py-2 border border-black text-center">Nilai {{ $cpl }}
                        </th>
                        <th rowspan="3" class="px-4 py-2 border border-black text-center">Capaian {{ $cpl }}
                        </th>
                        <th rowspan="3" class="px-4 py-2 border border-black text-center">Capaian {{ $cpl }}
                            Tidak Full</th>
                    @endforeach
                </tr>

                <!-- Baris kedua: CPMK -->
                <tr class="border-b border-black bg-gray-100">
                    @foreach ($mks as $mk)
                        @foreach ($mk->cpmks as $cpmk)
                            <th class="px-4 py-2 border border-black text-center">
                                @if ($cpmk->cplMk && $cpmk->cplMk->cpl)
                                    {{ $cpmk->cplMk->cpl->nama_cpl }}
                                @else
                                    -
                                @endif
                            </th>
                        @endforeach
                    @endforeach
                </tr>

                <!-- Baris ketiga: CPMK -->
                <tr class="border-b border-black">
                    @foreach ($mks as $mk)
                        @foreach ($mk->cpmks as $cpmk)
                            <th class="px-4 py-2 border border-black text-center">{{ $cpmk->kode_cpmk }}</th>
                        @endforeach
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
                        <td class="px-4 py-2 border border-black text-center font-bold">
                            {{ $totalBobotMk > 0 ? number_format($totalBobotMk, 2) : '-' }}
                        </td>
                    @endforeach

                    <!-- Total CPL diambil dari jumlah bobot CPMK yang berhubungan dengan CPL -->
                    @foreach ($cplList as $cpl)
                        @php
                            $totalCplKeseluruhan = $bobotTotalCpl[$cpl] ?? 0;
                        @endphp
                        <td class="px-4 py-2 border border-black text-center bg-gray-300">
                            {{ $totalCplKeseluruhan > 0 ? number_format($totalCplKeseluruhan, 2) : '-' }}
                        </td>
                        <td class="px-4 py-2 border border-black text-center bg-gray-300">
                            100%
                        </td>
                        <td class="px-4 py-2 border border-black text-center bg-gray-300">
                            100%
                        </td>
                    @endforeach
                </tr>

                <!-- Row untuk Mahasiswa -->
                @php $no = 1; @endphp
                @foreach ($mahasiswa as $mhs)
                    <tr class="border-b border-black">
                        <td class="px-4 py-2 border border-black text-center">{{ $no++ }}</td>
                        <td class="px-4 py-2 border border-black text-center">{{ $mhs['nama'] }}</td>
                        @foreach ($mks as $mk)
                            @foreach ($mk->cpmks as $cpmk)
                                <td class="px-4 py-2 border border-black text-center">
                                    {{ isset($mhs['nilai_cpmk'][$mk->nama_mk][$cpmk->kode_cpmk])
                                        ? number_format($mhs['nilai_cpmk'][$mk->nama_mk][$cpmk->kode_cpmk])
                                        : '-' }}
                                </td>
                            @endforeach
                            <td class="px-4 py-2 border border-black text-center font-bold">
                                {{ isset($mhs['nilai_mk'][$mk->nama_mk]) ? number_format($mhs['nilai_mk'][$mk->nama_mk], 2) : '-' }}
                            </td>
                        @endforeach

                        @foreach ($cplList as $cpl)
                            <td class="px-4 py-2 border border-black text-center">
                                {{ number_format($mhs['nilai_cpl'][$cpl] ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-2 border border-black text-center">
                                {{ round((($mhs['nilai_cpl'][$cpl] ?? 0) / ($bobotTotalCpl[$cpl] ?? 1)) * 100, 2) }}%
                            </td>

                            <td class="px-4 py-2 border border-black text-center">
                                {{ round((($mhs['nilai_cpl_tidak_full'][$cpl] ?? 0) / ($bobotTotalCpl[$cpl] ?? 1)) * 100, 2) }}%
                            </td>
                        @endforeach


                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
