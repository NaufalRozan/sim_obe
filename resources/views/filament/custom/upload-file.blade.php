<x-layouts.app>
    <div>
        <x-filament::breadcrumbs :breadcrumbs="[
            '/pengajar/cpmk-pengajars' => 'CPMK Matakuliah',
            '' => 'List',
        ]" />

        <div class="flex justify-between items-center mt-4">
            <div class="text-3xl font-bold">Nilai CPMK</div>
            <div>
                {{ $downloadExcelAction }}
            </div>
        </div>

        <div class="mt-6">
            <form wire:submit.prevent="save" class="w-full max-w-sm flex flex-col space-y-4">
                <div>
                    <label for="fileInput" class="block text-sm font-bold text-gray-700 mb-2">
                        Pilih Berkas
                    </label>
                    <div class="flex items-center space-x-4">
                        <input id="fileInput" type="file" wire:model="file"
                            class="w-full px-3 py-2 border rounded shadow appearance-none text-gray-700 focus:outline-none focus:shadow-outline">
                        <button type="submit"
                            class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-700 focus:outline-none focus:shadow-outline">
                            Upload
                        </button>
                    </div>
                    @error('file')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
