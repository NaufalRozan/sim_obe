<x-filament-panels::page>
    <h1 class="text-xl font-bold mb-4">{{ __('Pemetaan BK terhadap MK') }}</h1>

    @if ($mks->isEmpty() || $bks->isEmpty())
        <p class="text-center text-gray-500">{{ __('Data tidak tersedia') }}</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2 bg-gray-100">{{ __('Kode') }}</th>
                    <th class="border border-gray-300 p-2 bg-gray-100">{{ __('Nama Mata Kuliah') }}</th>
                    @foreach ($bks as $bk)
                        <th class="border border-gray-300 p-2 bg-gray-100 text-center">
                            {{ $bk->kode_bk }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($mks as $mk)
                    <tr>
                        <td class="border border-gray-300 p-2 text-center">{{ $mk->kode }}</td>
                        <td class="border border-gray-300 p-2">{{ $mk->nama_mk }}</td>
                        @foreach ($bks as $bk)
                            <td class="border border-gray-300 p-2 text-center">
                                @if ($mk->bks->contains($bk->id))
                                    ✔
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + count($bks) }}" class="text-center text-gray-500 p-4">
                            {{ __('Tidak ada mata kuliah yang tersedia') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</x-filament-panels::page>
