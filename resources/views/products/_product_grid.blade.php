@forelse($products as $product)
    <div class="overflow-hidden transition bg-white rounded-lg shadow-lg hover:shadow-xl">
        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ Storage::url($product->primary_image) }}"
                 alt="{{ $product->name }}"
                 class="object-cover w-full h-48">
        </a>
        <div class="p-4">
            <h3 class="mb-2 text-lg font-semibold">
                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary">
                    {{ $product->name }}
                </a>
            </h3>
            <p class="mb-2 text-sm text-gray-600">{{ $product->category->name }}</p>
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-primary">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
                <button type="button"
                        onclick="addToCart({{ json_encode($product) }}, {{ $product->variants_count > 0 }})"
                        class="p-2 text-white transition rounded-lg bg-primary hover:bg-primary/90">
                    @if($product->variants_count > 0)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-3 p-8 text-center bg-white rounded-lg">
        <p class="text-gray-500">Tidak ada produk yang ditemukan.</p>
    </div>
@endforelse

{{ $products->links() }}
