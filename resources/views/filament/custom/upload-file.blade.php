<style>
    .bg-success {
        background-color: #4caf50;
    }

    .bg-success:hover {
        background-color: #43a047;
    }

    .input-margin {
        margin-right: 8px;
        /* Menambahkan margin kanan pada input */
    }
</style>

<div>
    <x-filament::breadcrumbs :breadcrumbs="[
        '/pengajar/cpmk-pengajars' => 'CPMK Matakuliah',
        '' => 'List',
    ]" />
    <div class="flex justify-between mt-4">
        <div class="font-bold text-3xl">Nilai CPMK</div>
        <div>
            {{ $downloadExcelAction }}
        </div>
    </div>
    <div class="mt-4">
        <form wire:submit.prevent="save" class="w-full max-w-sm flex flex-col">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fileInput">
                    Pilih Berkas
                </label>
                <div class="flex items-center">
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline input-margin"
                        id="fileInput" type="file" wire:model="file">
                    <button
                        class="bg-success hover:bg-success-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                        Upload
                    </button>
                </div>
                @error('file')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </form>
    </div>
</div>
