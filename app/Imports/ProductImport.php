<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithChunkReading
{
    use SkipsErrors, Importable;

    private $rowCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        Log::channel('daily')->info("Processing row {$this->rowCount}", ['data' => $row]);

        try {
            if (empty($row['name']) || empty($row['category_name'])) {
                throw new \Exception('Required fields missing');
            }

            $category = Category::firstOrCreate(
                ['name' => $row['category_name']],
                ['slug' => Str::slug($row['category_name'])]
            );

            Log::info('Category processed', ['category' => $category->name]);

            $product = Product::create([
                'name' => $row['name'],
                'category_id' => $category->id,
                'slug' => Str::slug($row['name']),
                'description' => $row['description'] ?? '',
                'price' => (float) ($row['price'] ?? 0),
                'stock' => (int) ($row['stock'] ?? 0),
                'sku' => $row['sku'] ?? Str::random(10),
                'is_active' => strtoupper($row['is_active'] ?? 'YES') === 'YES',
            ]);

            Log::channel('daily')->info('Product created', ['product' => $product->toArray()]);
            return $product;
        } catch (\Exception $e) {
            Log::channel('daily')->error('Row import failed', [
                'row' => $row,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string'],
            '*.category_name' => ['required', 'string'],
            '*.description' => ['nullable', 'string'],
            '*.price' => ['nullable', 'numeric', 'min:0'],
            '*.stock' => ['nullable', 'integer', 'min:0'],
            '*.sku' => ['nullable', 'string', 'unique:products,sku'],
            '*.is_active' => ['nullable', 'string'],
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
