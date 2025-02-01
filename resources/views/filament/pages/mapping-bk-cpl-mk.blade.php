<x-filament-panels::page>
    <h1 class="text-xl font-bold mb-4">{{ __('Pemetaan BK - CPL - MK') }}</h1>

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
                                    // Ambil mata kuliah yang memiliki hubungan dengan BK dan CPL saat ini
                                    $relatedMks = $bk->mks
                                        ->filter(function ($mk) use ($cpl) {
                                            return $mk->cpls->contains($cpl->id);
                                        })
                                        ->pluck('kode')
                                        ->implode(', ');
                                @endphp

                                {{ $relatedMks }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-filament-panels::page>
