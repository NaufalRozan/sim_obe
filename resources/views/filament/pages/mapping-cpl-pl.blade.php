<x-filament-panels::page>
    <h1 class="text-xl font-bold mb-4">{{ __('Pemetaan CPL terhadap PL') }}</h1>

    @if ($pls->isEmpty() || $cpls->isEmpty())
        <p class="text-center text-gray-500">{{ __('Data tidak tersedia') }}</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2 bg-gray-100">{{ __('Kode') }}</th>
                    <th class="border border-gray-300 p-2 bg-gray-100">{{ __('Nama Profil Lulusan') }}</th>
                    @foreach ($cpls as $cpl)
                        <th class="border border-gray-300 p-2 bg-gray-100 text-center">
                            {{ $cpl->nama_cpl }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($pls as $pl)
                    <tr>
                        <td class="border border-gray-300 p-2 text-center">{{ $pl->kode }}</td>
                        <td class="border border-gray-300 p-2">{{ $pl->nama_pl }}</td>
                        @foreach ($cpls as $cpl)
                            <td class="border border-gray-300 p-2 text-center">
                                @if ($pl->cpls->contains($cpl->id))
                                    ✔
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + count($cpls) }}" class="text-center text-gray-500 p-4">
                            {{ __('Tidak ada data Profil Lulusan yang tersedia') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</x-filament-panels::page>
