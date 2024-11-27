<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\LihatLaporanMahasiswa;
use App\Livewire\LihatLaporanMahasiswaTable;

Route::get('/', function () {
    return view('welcome');
});

// Route for Lihat Laporan Mahasiswa
Route::get('/filament/lihat-laporan-mahasiswa/{krs_mahasiswa_id}/{mk_ditawarkan_id}/{cpl_id?}', LihatLaporanMahasiswa::class)
    ->name('filament.pages.lihat-laporan-mahasiswa');

// API route for CPMK data
Route::get('/api/cpmk-data', [LihatLaporanMahasiswaTable::class, 'apiGetCpmkData'])->name('api.cpmk-data');
