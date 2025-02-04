<x-app-layout>
    <!-- Add popup HTML at the top -->
    <div id="popup" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center space-x-3">
                    <div id="popup-icon" class="flex-shrink-0"></div>
                    <p id="popup-message" class="text-gray-800"></p>
                </div>
                <button onclick="closePopup()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Store variants data
        const variants = @json($product->variants);
        const basePrice = {{ $product->price }};

        // Function to format price to rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Function to change main product image
        function changeImage(imageUrl) {
            document.getElementById('mainImage').src = imageUrl;
        }

        // Variables to store selected variant and quantity
        let selectedVariantId = null;
        let quantity = 1;

        // Function to select product variant
        function selectVariant(variantId) {
            selectedVariantId = variantId;
            // Remove selected state from all variant buttons
            document.querySelectorAll('.variant-button').forEach(button => {
                button.classList.remove('border-primary');
                button.classList.add('border-gray-200');
            });
            // Add selected state to clicked button
            event.currentTarget.classList.remove('border-gray-200');
            event.currentTarget.classList.add('border-primary');

            // Update price based on selected variant
            const variant = variants.find(v => v.id === variantId);
            if (variant) {
                document.getElementById('product-price').innerText = 'Rp ' + formatRupiah(variant.price);
            } else {
                document.getElementById('product-price').innerText = 'Rp ' + formatRupiah(basePrice);
            }
        }

        // Functions to handle quantity
        function incrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value) || 1;
            input.value = currentValue + 1;
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value) || 2;
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        // Add popup functions
        function showPopup(message, type = 'success') {
            const popup = document.getElementById('popup');
            const popupMessage = document.getElementById('popup-message');
            const popupIcon = document.getElementById('popup-icon');

            popupMessage.textContent = message;

            if (type === 'success') {
                popupIcon.innerHTML = `
                    <div class="p-2 text-white bg-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                `;
            } else {
                popupIcon.innerHTML = `
                    <div class="p-2 text-white bg-red-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                `;
            }

            popup.classList.remove('hidden');

            setTimeout(() => {
                closePopup();
            }, 3000);
        }

        function closePopup() {
            document.getElementById('popup').classList.add('hidden');
        }

        // Function to add product to cart
        function addToCart() {
            const quantity = document.getElementById('quantity').value;
            const productId = {{ $product->id }};
            const hasVariants = {{ $product->variants->count() > 0 ? 'true' : 'false' }};
            const variantId = selectedVariantId;

            if (hasVariants && !variantId) {
                showPopup('Silakan pilih varian terlebih dahulu', 'error');
                return;
            }

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                updateCartCount(data.cart_count);
                document.getElementById('cart-dropdown').innerHTML = data.cartHtml;
                showPopup('Produk berhasil ditambahkan ke keranjang!');
            })
            .catch(error => {
                console.error('Error:', error);
                showPopup('Gagal menambahkan produk ke keranjang', 'error');
            });
        }
    </script>

    <style>
        /* Menghilangkan tombol spinner pada input number */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>

    <div class="container px-4 py-8 mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li><a href="/" class="text-gray-500 hover:text-primary">Beranda</a></li>
                <li class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a 1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-primary">Produk</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a 1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-gray-500">{{ $product->name }}</span>
                </li>
            </ol>
        </nav>

        <!-- Product Details -->
        <div class="grid grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                <div class="overflow-hidden bg-white rounded-lg aspect-w-1 aspect-h-1">
                    <img id="mainImage" src="{{ Storage::url($product->primary_image) }}"
                         alt="{{ $product->name }}"
                         class="object-cover w-full h-full">
                </div>
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                        <button type="button"
                                onclick="changeImage('{{ Storage::url($image->image) }}')"
                                class="overflow-hidden bg-white rounded-lg aspect-w-1 aspect-h-1">
                            <img src="{{ Storage::url($image->image) }}"
                                 alt="{{ $product->name }}"
                                 class="object-cover w-full h-full">
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-sm text-white rounded-full bg-primary">{{ $product->category->name }}</span>
                    <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                </div>

                <div class="text-3xl font-bold text-primary" id="product-price">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>

                @if($product->variants->count() > 0)
                    <div class="space-y-4">
                        <h3 class="font-semibold">Pilih Varian:</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($product->variants as $variant)
                                <button type="button"
                                        onclick="selectVariant({{ $variant->id }})"
                                        class="p-4 text-left border border-gray-200 rounded-lg variant-button hover:border-primary">
                                    <div class="font-medium">{{ $variant->name }}</div>
                                    <div class="text-sm text-gray-500">Rp {{ number_format($variant->price, 0, ',', '.') }}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex items-center space-x-4">
                    <div class="flex items-center border rounded-lg">
                        <button type="button" onclick="decrementQuantity()" class="flex items-center justify-center w-10 h-10 hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" id="quantity" value="1" min="1"
                               class="w-16 h-10 text-center border-x focus:outline-none"
                               readonly>
                        <button type="button" onclick="incrementQuantity()" class="flex items-center justify-center w-10 h-10 hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <button type="button" onclick="addToCart()" class="flex-1 btn-primary">
                        Tambah ke Keranjang
                    </button>
                </div>

                <div class="prose">
                    <h3 class="font-semibold">Deskripsi Produk:</h3>
                    <div class="mt-2">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="mb-8 text-2xl font-bold">Produk Terkait</h2>
                <div class="grid grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                            <a href="{{ route('products.show', $related->slug) }}">
                                <img src="{{ Storage::url($related->primary_image) }}"
                                     alt="{{ $related->name }}"
                                     class="object-cover w-full h-48">
                            </a>
                            <div class="p-4">
                                <h3 class="mb-2 text-lg font-semibold">
                                    <a href="{{ route('products.show', $related->slug) }}" class="hover:text-primary">
                                        {{ $related->name }}
                                    </a>
                                </h3>
                                <div class="text-lg font-bold text-primary">
                                    Rp {{ number_format($related->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
