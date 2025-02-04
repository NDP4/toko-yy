<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'week';

    protected function getFilters(): ?array
    {
        return [
            'day' => 'Today',
            'week' => 'Last 7 Days',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $data = match ($this->filter) {
            'day' => $this->getDayData(),
            'week' => $this->getWeekData(),
            'month' => $this->getMonthData(),
            'year' => $this->getYearData(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data['values'],
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    private function getDayData(): array
    {
        $orders = Order::where('created_at', '>=', now()->startOfDay())
            ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
            ->groupBy('hour')
            ->get();

        $labels = range(0, 23);
        $values = array_fill(0, 24, 0);

        foreach ($orders as $order) {
            $values[$order->hour] = $order->total;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getWeekData(): array
    {
        $orders = Order::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D');
            $values[] = $orders->firstWhere('date', $date)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getMonthData(): array
    {
        $orders = Order::where('created_at', '>=', now()->startOfMonth())
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->get();

        $labels = [];
        $values = [];
        $daysInMonth = now()->daysInMonth;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = now()->startOfMonth()->addDays($i - 1)->format('Y-m-d');
            $labels[] = $i;
            $values[] = $orders->firstWhere('date', $date)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getYearData(): array
    {
        $orders = Order::where('created_at', '>=', now()->startOfYear())
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->get();

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $values = array_fill(0, 12, 0);

        foreach ($orders as $order) {
            $values[$order->month - 1] = $order->total;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string  // Changed from protected to public
    {
        return 'Sales Overview';
    }
}
