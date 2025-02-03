<x-filament-panels::page>
    <h1 class="text-xl font-bold mb-4">{{ __('Pemetaan BK terhadap CPL') }}</h1>

    @if ($bks->isEmpty() || $cpls->isEmpty())
        <p class="text-center text-gray-500">{{ __('Data tidak tersedia') }}</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2 bg-blue-300">{{ __('BK\CPL') }}</th>
                    @foreach ($cpls as $cpl)
                        <th class="border border-gray-300 p-2 bg-blue-300 text-center">
                            {{ $cpl->nama_cpl }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($bks as $bk)
                    <tr>
                        <td class="border border-gray-300 p-2 font-bold">{{ $bk->kode_bk }}</td>
                        @foreach ($cpls as $cpl)
                            <td class="border border-gray-300 p-2 text-center">
                                @php
                                    // Cek apakah ada MK yang menghubungkan BK ini dengan CPL ini
                                    $hasConnection = $mks
                                        ->filter(function ($mk) use ($bk, $cpl) {
                                            return $mk->bks->contains($bk->id) && $mk->cpls->contains($cpl->id);
                                        })
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
    @endif
</x-filament-panels::page>
