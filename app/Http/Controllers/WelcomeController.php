<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class WelcomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $featuredProducts = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $newArrivals = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('categories', 'featuredProducts', 'newArrivals'));
    }
}
