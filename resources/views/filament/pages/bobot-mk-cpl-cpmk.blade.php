<x-filament-panels::page>
    <div class="p-6 space-y-4">
        <h2 class="text-xl font-bold text-center">Pemetaan MK - CPL - CPMK dengan Bobot Penilaian</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 bg-white text-sm text-left">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">MK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">CPL</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">CPMK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Deskripsi CPMK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Bobot CPMK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mks as $mk)
                        @php
                            $totalCpmks = max(1, $mk->cpmks->count());
                            $totalBobotMk = $mk->cpmks->sum('bobot');
                            $firstMk = true;
                        @endphp

                        @foreach ($mk->cpmks as $index => $cpmk)
                            <tr class="border-b border-gray-300">
                                @if ($firstMk)
                                    <td class="px-4 py-2 border border-gray-300 text-center"
                                        rowspan="{{ $totalCpmks }}">
                                        {{ $mk->kode }}
                                    </td>

                                    @php $firstMk = false; @endphp
                                @endif

                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    {{ $cpmk->cplMk->cpl->nama_cpl ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-300 text-center">{{ $cpmk->kode_cpmk }}</td>
                                <td class="px-4 py-2 border border-gray-300 text-left">{{ $cpmk->deskripsi ?? '-' }}
                                </td>
                                <td class="px-4 py-2 border border-gray-300 text-center">{{ $cpmk->bobot ?? '-' }}</td>
                            </tr>
                        @endforeach

                        <!-- Baris Total Bobot untuk MK -->
                        <tr class="border-b border-gray-300 font-bold bg-gray-100">
                            <td colspan="4" class="px-4 py-2 border border-gray-300 text-right">Total Bobot MK
                                ({{ $mk->kode }}):</td>
                            <td class="px-4 py-2 border border-gray-300 text-center">{{ $totalBobotMk }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
