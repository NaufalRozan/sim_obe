<x-filament-panels::page>
    <h1 class="text-xl font-bold text-center mb-4">{{ __('Pemetaan BK terhadap CPL') }}</h1>

    @if ($bks->isEmpty() || $cpls->isEmpty())
        <p class="text-center text-gray-500">{{ __('Data tidak tersedia') }}</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 bg-white text-sm text-left">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">{{ __('BK \\ CPL') }}</th>
                        @foreach ($cpls as $cpl)
                            <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">
                                {{ $cpl->nama_cpl }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bks as $bk)
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-2 border border-gray-300 font-bold text-center">{{ $bk->kode_bk }}</td>
                            @foreach ($cpls as $cpl)
                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    @php
                                        $hasConnection = $mks
                                            ->filter(
                                                fn($mk) => $mk->bks->contains($bk->id) && $mk->cpls->contains($cpl->id),
                                            )
                                            ->isNotEmpty();
                                    @endphp

                                    @if ($hasConnection)
                                        ✔
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
