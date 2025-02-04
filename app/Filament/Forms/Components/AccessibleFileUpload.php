<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Log;

class AccessibleFileUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);

        $this->afterStateHydrated(function (FileUpload $component, $state): void {
            if (blank($state)) {
                return;
            }

            $component->state([]);
        });

        $this->id(fn() => $this->getName() . '-' . uniqid());

        $this->beforeStateDehydrated(function () {
            try {
                // Add any additional validation or checks here
            } catch (\Exception $e) {
                Log::error('File upload error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->addError('file', 'Error processing file: ' . $e->getMessage());
            }
        });
    }
}
