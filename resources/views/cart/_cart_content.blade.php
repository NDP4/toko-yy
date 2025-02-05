@if($cart && $cart->items->count() > 0)
    <div class="grid grid-cols-12 gap-8">
        <!-- Cart Items -->
        <div class="col-span-8">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 space-y-6">
                    @foreach($cart->items as $item)
                        <div class="flex items-center pb-6 space-x-6 border-b last:border-0 last:pb-0">
                            <img src="{{ Storage::url($item->product->primary_image) }}"
                                 alt="{{ $item->product->name }}"
                                 class="object-cover w-24 h-24 rounded-lg">

                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                @if($item->variant)
                                    <p class="text-gray-500">{{ $item->variant->name }}</p>
                                @endif
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="font-medium text-primary">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="flex items-center border rounded-lg">
                                    <button type="button"
                                            onclick="event.preventDefault(); updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="flex items-center justify-center w-8 h-8 hover:bg-gray-100">
                                        -
                                    </button>
                                    <input type="number"
                                           value="{{ $item->quantity }}"
                                           min="1"
                                           class="w-12 h-8 text-center border-x focus:outline-none"
                                           readonly>
                                    <button type="button"
                                            onclick="event.preventDefault(); updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            class="flex items-center justify-center w-8 h-8 hover:bg-gray-100">
                                        +
                                    </button>
                                </div>

                                <button onclick="event.preventDefault(); removeItem({{ $item->id }})"
                                        class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-span-4">
            <div class="sticky top-4 p-6 bg-white rounded-lg shadow">
                <h2 class="mb-4 text-lg font-semibold">Ringkasan Pesanan</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Item</span>
                        <span>{{ $cart->items->sum('quantity') }} item</span>
                    </div>
                    <div class="pt-3 border-t">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-primary">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @auth
                    <form action="{{ route('checkout.process') }}" method="GET" class="mt-6">
                        <button type="submit" class="w-full btn-primary">
                            Lanjut ke Pembayaran
                        </button>
                    </form>
                @else
                    <div class="mt-6 space-y-4">
                        <a href="{{ route('login') }}?redirect=cart" class="block w-full text-center btn-primary">
                            Masuk untuk Checkout
                        </a>
                        <p class="text-sm text-center text-gray-500">
                            Belum punya akun?
                            <a href="{{ route('register') }}?redirect=cart" class="text-primary hover:underline">
                                Daftar sekarang
                            </a>
                        </p>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@else
    <div class="py-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <h2 class="mb-2 text-xl font-medium text-gray-900">Keranjang Belanja Kosong</h2>
        <p class="mb-6 text-gray-500">Anda belum menambahkan produk ke keranjang.</p>
        <a href="{{ route('products.index') }}" class="btn-primary">
            Mulai Belanja
        </a>
    </div>
@endif
