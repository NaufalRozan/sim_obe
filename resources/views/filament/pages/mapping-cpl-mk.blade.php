<x-filament-panels::page>
    <h1 class="text-xl font-bold text-center mb-4">{{ __('Pemetaan CPL terhadap MK') }}</h1>

    @if ($mks->isEmpty() || $cpls->isEmpty())
        <p class="text-center text-gray-500">{{ __('Data tidak tersedia') }}</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 bg-white text-sm text-left">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">{{ __('Kode') }}</th>
                        <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">{{ __('Nama Mata Kuliah') }}
                        </th>
                        @foreach ($cpls as $cpl)
                            <th class="px-4 py-2 border border-gray-300 text-center bg-gray-200">
                                {{ $cpl->nama_cpl }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mks as $mk)
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-2 border border-gray-300 text-center">{{ $mk->kode }}</td>
                            <td class="px-4 py-2 border border-gray-300">{{ $mk->nama_mk }}</td>
                            @foreach ($cpls as $cpl)
                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    @if ($mk->cpls->contains($cpl->id))
                                        ✔
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($cpls) }}"
                                class="px-4 py-4 border border-gray-300 text-center text-gray-500">
                                {{ __('Tidak ada mata kuliah yang tersedia') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
