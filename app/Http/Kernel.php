<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...existing code...

    protected $routeMiddleware = [
        // ...existing middlewares...
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ];
}
