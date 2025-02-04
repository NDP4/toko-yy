<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product')->tabs([
                    Forms\Components\Tabs\Tab::make('Basic Information')
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->required()
                                ->label('Category')
                                ->id('category-select'),
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, callable $set) {
                                    $set('slug', $state ? Str::slug($state) : '');
                                })
                                ->id('product-name'),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->id('product-slug'),
                            Forms\Components\RichEditor::make('description')
                                ->required()
                                ->columnSpanFull()
                                ->id('product-description'),
                            Forms\Components\TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->id('product-price'),
                            Forms\Components\TextInput::make('stock')
                                ->required()
                                ->numeric()
                                ->id('product-stock'),
                            Forms\Components\TextInput::make('sku')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->id('product-sku'),
                            Forms\Components\Toggle::make('is_active')
                                ->required()
                                ->id('product-is-active'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Images')
                        ->schema([
                            Forms\Components\Repeater::make('images')
                                ->relationship()
                                ->schema([
                                    Forms\Components\FileUpload::make('image')
                                        ->image()
                                        ->required(),
                                    Forms\Components\Toggle::make('is_primary')
                                        ->default(false),
                                    Forms\Components\TextInput::make('sort_order')
                                        ->numeric()
                                        ->default(0),
                                ])
                                ->columns(3)
                                ->columnSpanFull()
                        ]),

                    Forms\Components\Tabs\Tab::make('Variants')
                        ->schema([
                            Forms\Components\Repeater::make('variants')
                                ->relationship()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required(),
                                    Forms\Components\TextInput::make('sku')
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                    Forms\Components\TextInput::make('price')
                                        ->required()
                                        ->numeric()
                                        ->prefix('Rp'),
                                    Forms\Components\TextInput::make('stock')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\KeyValue::make('attributes')
                                        ->keyLabel('Attribute')
                                        ->valueLabel('Value'),
                                    Forms\Components\Toggle::make('is_active')
                                        ->default(true),
                                ])
                                ->columns(3)
                                ->columnSpanFull()
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
