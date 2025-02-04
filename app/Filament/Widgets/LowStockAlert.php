<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<=', 10)
                    ->orderBy('stock')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->alignRight(),
            ])
            ->striped()
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }

    public function getHeading(): string|View
    {
        return view('filament.widgets.low-stock-heading', [
            'stockCount' => Product::where('stock', '<=', 10)->count(),
        ]);
    }
}
