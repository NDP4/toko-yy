<x-app-layout>
    <div class="container py-8 mx-auto">
        <h1 class="text-2xl font-bold mb-6">Keranjang Belanja</h1>

        @if($cart && $cart->items->count() > 0)
            <div class="grid grid-cols-12 gap-8">
                <!-- Cart Items -->
                <div class="col-span-8">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 space-y-6">
                            @foreach($cart->items as $item)
                                <div class="flex items-center space-x-6 pb-6 border-b last:border-0 last:pb-0">
                                    <img src="{{ Storage::url($item->product->primary_image) }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-24 h-24 object-cover rounded-lg">

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        @if($item->variant)
                                            <p class="text-gray-500">{{ $item->variant->name }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center space-x-4">
                                            <span class="text-primary font-medium">
                                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center border rounded-lg">
                                            <button type="button"
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100">
                                                -
                                            </button>
                                            <input type="number"
                                                   value="{{ $item->quantity }}"
                                                   min="1"
                                                   class="w-12 h-8 text-center border-x focus:outline-none"
                                                   readonly>
                                            <button type="button"
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100">
                                                +
                                            </button>
                                        </div>

                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-span-4">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total ({{ $cart->items->sum('quantity') }} item)</span>
                                <span class="font-medium">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-3 border-t">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <button class="w-full btn-primary mt-6">
                            Lanjut ke Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-900 mb-2">Keranjang Belanja Kosong</h2>
                <p class="text-gray-500 mb-6">Anda belum menambahkan produk ke keranjang.</p>
                <a href="{{ route('products.index') }}" class="btn-primary">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>

    <script>
        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;

            fetch(`/cart/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            }).then(() => {
                window.location.reload();
            });
        }
    </script>
</x-app-layout>
