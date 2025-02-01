<x-filament-panels::page>
    <h1 class="text-xl font-bold mb-4">{{ __('Pemetaan CPL terhadap MK') }}</h1>

    @if ($mks->isEmpty() || $cpls->isEmpty())
        <p class="text-center text-gray-500">{{ __('Data tidak tersedia') }}</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2 bg-gray-100">{{ __('Kode') }}</th>
                    <th class="border border-gray-300 p-2 bg-gray-100">{{ __('Nama Mata Kuliah') }}</th>
                    @foreach ($cpls as $cpl)
                        <th class="border border-gray-300 p-2 bg-gray-100 text-center">
                            {{ $cpl->nama_cpl }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($mks as $mk)
                    <tr>
                        <td class="border border-gray-300 p-2 text-center">{{ $mk->kode }}</td>
                        <td class="border border-gray-300 p-2">{{ $mk->nama_mk }}</td>
                        @foreach ($cpls as $cpl)
                            <td class="border border-gray-300 p-2 text-center">
                                @if ($mk->cpls->contains($cpl->id))
                                    ✔
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + count($cpls) }}" class="text-center text-gray-500 p-4">
                            {{ __('Tidak ada mata kuliah yang tersedia') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</x-filament-panels::page>
