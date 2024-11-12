<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\LaporanResource\Pages;
use App\Models\Laporan;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Filament\Pengajar\Resources\LaporanResource\Widgets\LaporanChart;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanResource extends Resource
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
            'index' => Pages\LaporanPage::route('/'),
        ];
    }
}
