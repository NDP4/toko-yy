<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Total Revenue' => Order::sum('total_amount'),
                'Total Orders' => Order::count(),
                'Total Products' => Product::count(),
                'Total Customers' => User::where('role', 'customer')->count(),
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Total Revenue',
            'Total Orders',
            'Total Products',
            'Total Customers',
        ];
    }
}
