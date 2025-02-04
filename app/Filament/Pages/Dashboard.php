<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportDashboard')
                ->color('success')
                ->size('lg'),
        ];
    }

    public function exportDashboard()
    {
        return Excel::download(new DashboardExport, 'dashboard-export.xlsx');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \Filament\Widgets\StatsOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\SalesChart::class,
            \App\Filament\Widgets\TopProductsByRevenue::class,
            \App\Filament\Widgets\LatestOrders::class,
            \App\Filament\Widgets\CustomerActivity::class,
            \App\Filament\Widgets\PopularProducts::class,
            \App\Filament\Widgets\LowStockAlert::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \Filament\Widgets\StatsOverviewWidget::make([
                \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Revenue', 'Rp ' . number_format(Order::sum('total_amount'), 2))
                    ->description('Total pendapatan keseluruhan')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->chart([7, 4, 6, 8, 5, 9, 10])
                    ->color('success'),

                \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Orders', Order::count())
                    ->description('Jumlah pesanan')
                    ->descriptionIcon('heroicon-m-shopping-cart')
                    ->chart([3, 5, 7, 4, 8, 6, 9])
                    ->color('info'),

                \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Products', Product::count())
                    ->description('Jumlah produk tersedia')
                    ->descriptionIcon('heroicon-m-cube')
                    ->chart([8, 6, 4, 7, 5, 9, 2])
                    ->color('warning'),

                \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Customers', User::where('role', 'customer')->count())
                    ->description('Jumlah pelanggan aktif')
                    ->descriptionIcon('heroicon-m-users')
                    ->chart([5, 7, 9, 4, 6, 8, 3])
                    ->color('primary'),
            ]),
        ];
    }
}
