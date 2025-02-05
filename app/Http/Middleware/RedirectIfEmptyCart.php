<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Cart;

class RedirectIfEmptyCart
{
    public function handle(Request $request, Closure $next)
    {
        $cart = Cart::getCart()->load('items');  // Added eager loading

        if (!$cart || $cart->items->isEmpty()) {  // Changed check method
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong');
        }

        $request->merge(['cart' => $cart]);
        return $next($request);
    }
}
