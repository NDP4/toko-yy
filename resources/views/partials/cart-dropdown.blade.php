@if($cart && $cart->items->count() > 0)
    <div class="p-4 space-y-4">
        <div class="space-y-3 overflow-y-auto max-h-64">
            @foreach($cart->items as $item)
                <div class="flex items-center space-x-4 group/item">
                    <img src="{{ Storage::url($item->product->primary_image) }}"
                         alt="{{ $item->product->name }}"
                         class="object-cover w-12 h-12 rounded">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</h4>
                        @if($item->variant)
                            <p class="text-xs text-gray-500">{{ $item->variant->name }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-gray-500">{{ $item->quantity }}x</span>
                            <span class="text-sm font-medium text-primary">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('cart.remove', $item) }}"
                          method="POST"
                          class="hidden group-hover/item:block"
                          onsubmit="return confirm('Hapus item ini dari keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 text-gray-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-900">Total</span>
                <span class="text-lg font-bold text-primary">
                    Rp {{ number_format($cart->total, 0, ',', '.') }}
                </span>
            </div>
            <a href="{{ route('cart.index') }}" class="block w-full py-2 text-sm text-center text-white transition rounded-lg bg-primary hover:bg-primary/90">
                Lihat Keranjang
            </a>
        </div>
    </div>
@else
    <div class="p-4 text-center text-gray-500">
        Keranjang belanja kosong
    </div>
@endif
