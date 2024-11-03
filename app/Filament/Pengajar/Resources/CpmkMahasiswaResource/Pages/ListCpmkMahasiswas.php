<?php

namespace App\Filament\Pengajar\Resources\CpmkMahasiswaResource\Pages;

use App\Exports\CpmkMahasiswaTemplateExport;
use App\Filament\Pengajar\Resources\CpmkMahasiswaResource;
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
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new CpmkMahasiswaTemplateExport($mkDitawarkanId),
                            'template_cpmk.xlsx'
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
