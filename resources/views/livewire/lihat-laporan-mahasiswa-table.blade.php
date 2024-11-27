<div>
    <div class="mb-4">
        <label for="cpl-filter" class="block text-sm font-medium text-gray-700">Filter CPL</label>
        <select id="cpl-filter"
            class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            wire:model="cpl_id">
            <option value="">Semua CPL</option>
            @foreach ($cplOptions as $id => $nama)
                <option value="{{ $id }}">{{ $nama }}</option>
            @endforeach
        </select>
    </div>

    <table id="cpmk-table" class="table-auto w-full mb-4">
        <tbody>
            @foreach ($cpmkData as $kategori => $records)
                <tr>
                    <td class="border px-4 py-2 font-semibold" rowspan="{{ max(1, $records->count()) }}">
                        {{ $kategori }}
                    </td>
                    @if ($records->count() > 0)
                        <td class="border px-4 py-2">{{ $records[0]['kode_cpmk'] }}</td>
                    @else
                        <td class="border px-4 py-2">-</td>
                    @endif
                </tr>
                @foreach ($records->slice(1) as $record)
                    <tr>
                        <td class="border px-4 py-2">{{ $record['kode_cpmk'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filter = document.getElementById('cpl-filter');

        filter.addEventListener('change', function() {
            const cplId = filter.value || '';

            // Update URL tanpa reload halaman
            const currentUrl = new URL(window.location.href);
            const baseUrl = currentUrl.origin + currentUrl.pathname.split('/').slice(0, 5).join('/');
            const newUrl = cplId ? `${baseUrl}/${cplId}` : baseUrl;

            history.pushState(null, '', newUrl);

            // Emit event ke Livewire
            Livewire.emit('filterCplUpdated', cplId);

            console.log('Event Livewire dikirim untuk CPL ID:', cplId);
        });
    });
</script>
