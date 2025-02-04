<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CreateProductTemplateCommand extends Command
{
    protected $signature = 'products:create-template';
    protected $description = 'Create product import template';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['name', 'category_name', 'description', 'price', 'stock', 'sku', 'is_active'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        // Add sample row
        $sampleData = [
            'Product Name',
            'Category Name',
            'Product Description',
            '100000',
            '10',
            'SKU001',
            'YES'
        ];
        foreach ($sampleData as $index => $value) {
            $sheet->setCellValue(chr(65 + $index) . '2', $value);
        }

        // Create templates directory if it doesn't exist
        $directory = storage_path('app/public/templates');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save file
        $writer = new Xlsx($spreadsheet);
        $writer->save($directory . '/product_import_template.xlsx');

        $this->info('Template created successfully!');
    }
}
