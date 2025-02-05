<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $rajaOngkirKey = '70f308a0e275d838eef096bf8ef71687';
    protected $rajaOngkirUrl = 'https://api.rajaongkir.com/starter';

    public function index(Request $request)
    {
        $cart = $request->cart ?? Cart::getCart();
        $cart->load(['items.product', 'items.variant']);

        if (!$cart->items->count()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong');
        }

        $provinces = $this->getProvinces();
        return view('checkout.index', compact('cart', 'provinces'));
    }

    public function getCities($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->rajaOngkirKey
            ])->get($this->rajaOngkirUrl . '/city', [
                'province' => $provinceId
            ]);

            $result = $response->json();
            return response()->json([
                'success' => true,
                'data' => $result['rajaongkir']['results'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Cities Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kota'
            ]);
        }
    }

    public function getShippingCost(Request $request)
    {
        try {
            $validated = $request->validate([
                'destination' => 'required',
                'weight' => 'required|numeric|min:1',
                'courier' => 'required|in:jne,pos,tiki'
            ]);

            // Convert weight to grams if needed
            $weight = $validated['weight'];
            if ($weight < 100) { // Assume weight is in kg if less than 100
                $weight *= 1000;
            }

            $response = Http::withHeaders([
                'key' => $this->rajaOngkirKey
            ])->post($this->rajaOngkirUrl . '/cost', [
                'origin' => '501', // Yogyakarta
                'destination' => $validated['destination'],
                'weight' => ceil($weight), // Round up weight to nearest gram
                'courier' => $validated['courier']
            ]);

            $result = $response->json();
            Log::info('RajaOngkir Response:', $result); // Debug logging

            if (!isset($result['rajaongkir']['results'][0]['costs'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data ongkir tidak tersedia untuk rute ini'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $result['rajaongkir']['results'][0]['costs']
            ]);
        } catch (\Exception $e) {
            Log::error('Shipping Cost Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat biaya pengiriman'
            ]);
        }
    }

    protected function getProvinces()
    {
        $response = Http::withHeaders([
            'key' => $this->rajaOngkirKey
        ])->get($this->rajaOngkirUrl . '/province');

        return $response->json()['rajaongkir']['results'];
    }

    public function process(Request $request)
    {
        $cart = Cart::getCart()->load(['items.product', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong');
        }

        // Validasi stok produk
        foreach ($cart->items as $item) {
            if ($item->variant) {
                if ($item->variant->stock < $item->quantity) {
                    return redirect()->route('cart.index')
                        ->with('error', "Stok {$item->product->name} - {$item->variant->name} tidak mencukupi");
                }
            } else {
                if ($item->product->stock < $item->quantity) {
                    return redirect()->route('cart.index')
                        ->with('error', "Stok {$item->product->name} tidak mencukupi");
                }
            }
        }

        $provinces = $this->getProvinces(); // Get provinces data
        return view('checkout.index', compact('cart', 'provinces'));
    }
}
