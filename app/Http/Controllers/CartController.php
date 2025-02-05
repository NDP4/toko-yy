<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::getCart()->load(['items.product', 'items.variant']);

        // Save redirect URL for login/register
        if ($request->has('redirect')) {
            session()->put('redirect_after_login', $request->redirect);
        }

        if ($request->ajax()) {
            return view('cart._cart_content', compact('cart'))->render();
        }

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('variants')->findOrFail($request->product_id);

        // Check if product has variants but no variant_id provided
        if ($product->variants->count() > 0 && !$request->variant_id) {
            return response()->json([
                'error' => 'Variant selection is required for this product',
                'message' => 'Silakan pilih varian terlebih dahulu'
            ], 422);
        }

        $cart = Cart::getCart();

        $price = $request->variant_id
            ? ProductVariant::findOrFail($request->variant_id)->price
            : $product->price;

        $existingItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $request->quantity
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $price
            ]);
        }

        // Reload cart with relationships for dropdown
        $cart->load(['items.product', 'items.variant']);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_count' => $cart->items->sum('quantity'),
            'cartHtml' => view('partials.cart-dropdown', ['cart' => $cart])->render()
        ]);
    }

    public function update(Request $request, CartItem $item)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $item->update($validated);
            $cart = Cart::getCart()->load(['items.product', 'items.variant']);

            return response()->json([
                'success' => true,
                'cart_count' => $cart->items->sum('quantity'),
                'cartHtml' => view('partials.cart-dropdown', ['cart' => $cart])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'cart_count' => 0,
                'cartHtml' => view('partials.cart-dropdown', ['cart' => null])->render()
            ], 200); // Change to 200 to prevent error
        }
    }

    public function remove(CartItem $item)
    {
        try {
            $item->delete();
            $cart = Cart::getCart()->load(['items.product', 'items.variant']);

            return response()->json([
                'success' => true,
                'cart_count' => $cart->items->sum('quantity'),
                'cartHtml' => view('partials.cart-dropdown', ['cart' => $cart])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'cart_count' => 0,
                'cartHtml' => view('partials.cart-dropdown', ['cart' => null])->render()
            ], 200); // Change to 200 to prevent error
        }
    }

    protected function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);
        }

        $sessionId = session()->get('cart_id');
        if (!$sessionId) {
            $sessionId = Str::uuid();
            session()->put('cart_id', $sessionId);
        }

        return Cart::firstOrCreate([
            'session_id' => $sessionId
        ]);
    }
}
