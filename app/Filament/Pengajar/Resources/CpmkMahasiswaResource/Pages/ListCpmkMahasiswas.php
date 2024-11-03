<?php

namespace App\Filament\Pengajar\Resources\CpmkMahasiswaResource\Pages;

use App\Exports\CpmkMahasiswaTemplateExport;
use App\Filament\Pengajar\Resources\CpmkMahasiswaResource;
use App\Models\MkDitawarkan;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCpmkMahasiswas extends ListRecords
{
    protected static string $resource = CpmkMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_excel')
                ->label('Download Template Nilai')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('success') // Ubah ke huruf kecil
                ->action(function () {
                    $mkDitawarkanId = request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id'); // Ambil dari request atau session
                    if ($mkDitawarkanId) {
                        // Ambil nama MK Ditawarkan
                        $mkDitawarkan = MkDitawarkan::find($mkDitawarkanId);
                        $namaMk = $mkDitawarkan ? $mkDitawarkan->mk->nama_mk : 'template_cpmk';

                        // Ganti karakter yang tidak valid di nama file dengan underscore
                        $namaFile = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $namaMk) . '.xlsx';

                        return Excel::download(
                            new CpmkMahasiswaTemplateExport($mkDitawarkanId),
                            $namaFile
                        );
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('MK Ditawarkan tidak ditemukan')
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }
}
