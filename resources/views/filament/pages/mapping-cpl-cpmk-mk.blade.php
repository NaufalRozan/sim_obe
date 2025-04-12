<x-filament-panels::page>
    <div class="p-6 space-y-4">
        <h2 class="text-xl font-bold text-center">Pemetaan CPL - CPMK - MK</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 bg-white text-sm text-left">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Kode CPL</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Deskripsi CPL</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Kode CPMK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Deskripsi CPMK</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Mata Kuliah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->cpls as $cpl)
                        @php
                            $totalMks = $cpl->mks->sum(fn($mk) => $mk->cpmks->count());
                        @endphp
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-2 border border-gray-300 font-bold align-top text-center" rowspan="{{ $totalMks ?: 1 }}">
                                {{ $cpl->nama_cpl }}
                            </td>
                            <td class="px-4 py-2 border border-gray-300 align-top text-justify" rowspan="{{ $totalMks ?: 1 }}">
                                {{ $cpl->deskripsi }}
                            </td>

                            @if ($cpl->mks->isEmpty())
                                <td class="px-4 py-2 border border-gray-300 text-center" colspan="3">
                                    Tidak ada data
                                </td>
                            @else
                                @php $firstMk = true; @endphp
                                @foreach ($cpl->mks as $mk)
                                    @php $totalCpmks = $mk->cpmks->count(); @endphp

                                    @foreach ($mk->cpmks as $index => $cpmk)
                                        @if (!$firstMk)
                                            <tr class="border-b border-gray-300">
                                        @endif

                                        <td class="px-4 py-2 border border-gray-300 text-center">
                                            {{ $cpmk->kode_cpmk }}
                                        </td>
                                        <td class="px-4 py-2 border border-gray-300 text-justify">
                                            {{ $cpmk->deskripsi }}
                                        </td>
                                        @if ($index === 0)
                                            <td class="px-4 py-2 border border-gray-300 text-center" rowspan="{{ $totalCpmks }}">
                                                {{ $mk->kode }}
                                            </td>
                                        @endif
                                        </tr>
                                        @php $firstMk = false; @endphp
                                    @endforeach
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
