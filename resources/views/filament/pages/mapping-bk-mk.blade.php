<x-filament-panels::page>
    <h1 class="text-xl font-bold mb-4">Pemetaan BK terhadap MK</h1>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2 bg-gray-100">Kode</th>
                <th class="border border-gray-300 p-2 bg-gray-100">Nama Mata Kuliah</th>
                @foreach ($bks as $bk)
                    <th class="border border-gray-300 p-2 bg-gray-100 text-center">
                        {{ $bk->kode_bk }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($mks as $mk)
                <tr>
                    <td class="border border-gray-300 p-2 text-center">{{ $mk->kode }}</td>
                    <td class="border border-gray-300 p-2">{{ $mk->nama_mk }}</td>
                    @foreach ($bks as $bk)
                        <td class="border border-gray-300 p-2 text-center">
                            @if ($bk->mks->contains($mk->id))
                                V
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament-panels::page>
