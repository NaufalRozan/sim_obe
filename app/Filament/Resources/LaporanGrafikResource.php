<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanGrafikResource\Pages;
use App\Filament\Resources\LaporanGrafikResource\RelationManagers;
use App\Models\LaporanGrafik;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanGrafikResource extends Resource
{
    use HasFiltersForm;

    // protected static ?string $model = Laporan::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $breadcrumb = 'Laporan Grafik';

    protected static ?string $navigationLabel = 'Laporan Grafik';

    protected static ?string $label = 'Laporan Grafik';

    public static function getPages(): array
    {
        return [
            'index' => Pages\LaporanGrafik::route('/'),
        ];
    }
}
