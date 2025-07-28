<?php

namespace App\Filament\Pengajar\Resources\CpmkMahasiswaResource\Pages;

use App\Exports\CpmkMahasiswaTemplateExport;
use App\Filament\Pengajar\Resources\CpmkMahasiswaResource;
use App\Imports\CpmkMahasiswaImport;
use App\Models\MkDitawarkan;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
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
                ->color('success')
                ->action(function () {
                    $mkDitawarkanId = request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id');

                    if ($mkDitawarkanId) {
                        $mkDitawarkan = MkDitawarkan::find($mkDitawarkanId);
                        $namaMk = $mkDitawarkan ? $mkDitawarkan->mk->nama_mk : 'template_cpmk';
                        $kelas = $mkDitawarkan ? $mkDitawarkan->kelas : 'Kelas';
                        $namaFile = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', "{$namaMk} - {$kelas}.xlsx");

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

    public function getHeader(): ?View
    {
        $downloadExcelAction = $this->getHeaderActions()[0];
        return view('filament.custom.upload-file', compact('downloadExcelAction'));
    }

    public $file;

    public function save()
    {
        if ($this->file) {
            try {
                $mkDitawarkanId = request('mk_ditawarkan_id') ?? session('mk_ditawarkan_id');

                if (!$mkDitawarkanId) {
                    \Filament\Notifications\Notification::make()
                        ->title('MK Ditawarkan tidak ditemukan')
                        ->warning()
                        ->send();
                    return;
                }

                Excel::import(new CpmkMahasiswaImport($mkDitawarkanId), $this->file);

                \Filament\Notifications\Notification::make()
                    ->title('Berhasil')
                    ->body('Nilai CPMK berhasil diunggah.')
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                \Filament\Notifications\Notification::make()
                    ->title('Gagal')
                    ->body('Terjadi kesalahan saat mengunggah file: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }
        } else {
            \Filament\Notifications\Notification::make()
                ->title('File Tidak Ditemukan')
                ->body('Silakan pilih file untuk diunggah.')
                ->warning()
                ->send();
        }
    }
}
