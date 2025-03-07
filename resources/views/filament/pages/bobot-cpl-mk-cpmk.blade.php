<x-filament-panels::page>
    <div class="p-6 space-y-4">
        <h2 class="text-xl font-bold text-center">Pemetaan CPL - MK - CPMK dengan Bobot Penilaian</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 bg-white text-sm text-left">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">CPL</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">MK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">CPMK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Deskripsi CPMK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Bobot CPMK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cpls as $cpl)
                        @php
                            // Menghitung total baris untuk CPL berdasarkan jumlah MK dan CPMK
                            $totalRowsForCpl = $cpl->mks->sum(fn($mk) => max(1, $mk->cpmks->count()));
                            $totalBobotCpl = $cpl->mks->flatMap->cpmks->sum('bobot');
                            $firstCpl = true;
                        @endphp

                        @foreach ($cpl->mks as $mk)
                            @php
                                $totalCpmks = max(1, $mk->cpmks->count());
                                $totalBobotMk = $mk->cpmks->sum('bobot');
                                $firstMk = true;
                            @endphp

                            @foreach ($mk->cpmks as $index => $cpmk)
                                <tr class="border-b border-gray-300">
                                    @if ($firstCpl)
                                        <td class="px-4 py-2 border border-gray-300 font-bold align-top text-center bg-yellow-200"
                                            rowspan="{{ $totalRowsForCpl }}">
                                            {{ $cpl->nama_cpl }}
                                        </td>
                                        @php $firstCpl = false; @endphp
                                    @endif

                                    @if ($firstMk)
                                        <td class="px-4 py-2 border border-gray-300 text-center"
                                            rowspan="{{ $totalCpmks }}">
                                            {{ $mk->kode }}
                                        </td>
                                        @php $firstMk = false; @endphp
                                    @endif

                                    <td class="px-4 py-2 border border-gray-300 text-center">{{ $cpmk->kode_cpmk }}</td>
                                    <td class="px-4 py-2 border border-gray-300 text-left">{{ $cpmk->deskripsi ?? '-' }}
                                    </td>

                                    @if ($index === 0)
                                        <td class="px-4 py-2 border border-gray-300 text-center"
                                            rowspan="{{ $totalCpmks }}">
                                            {{ $totalBobotMk }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach

                        <!-- Baris Total Bobot untuk CPL -->
                        <tr class="border-b border-gray-300 font-bold bg-gray-100">
                            <td colspan="4" class="px-4 py-2 border border-gray-300 text-right">Total Bobot CPL
                                ({{ $cpl->nama_cpl }}):</td>
                            <td class="px-4 py-2 border border-gray-300 text-center">{{ $totalBobotCpl }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
