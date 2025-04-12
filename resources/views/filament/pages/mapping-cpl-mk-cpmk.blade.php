<x-filament-panels::page>
    <div class="p-6 space-y-4">
        <h2 class="text-xl font-bold text-center">Pemetaan CPL - MK - CPMK</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 bg-white text-sm text-left">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">Kode MK</th>
                        @foreach ($cpls as $cpl)
                            <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">{{ $cpl->nama_cpl }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mks as $mk)
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-2 border border-gray-300 font-bold text-center">
                                {{ $mk->kode }}
                            </td>
                            @foreach ($cpls as $cpl)
                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    @php
                                        $cpmks = $mk->cpmks->where('cpl_mk_id', $cpl->id);
                                    @endphp
                                    @if ($cpmks->isNotEmpty())
                                        {{ $cpmks->pluck('kode_cpmk')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
