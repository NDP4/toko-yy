<x-app-layout>
    <!-- Replace toast with popup -->
    <div id="popup" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center space-x-3">
                    <div id="popup-icon" class="flex-shrink-0"></div>
                    <p id="popup-message" class="text-gray-800"></p>
                </div>
                <button onclick="closePopup()" class="absolute text-gray-400 top-4 right-4 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Variant Selection Modal -->
    <div id="variantModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" id="modalProductName"></h3>
                    <button onclick="closeVariantModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div id="variantsList" class="grid grid-cols-2 gap-4"></div>
                    <div class="flex items-center mt-4 space-x-4">
                        <div class="flex items-center border rounded-lg">
                            <button type="button" onclick="decrementModalQuantity()" class="flex items-center justify-center w-8 h-8 hover:bg-gray-100">-</button>
                            <input type="number" id="modalQuantity" value="1" min="1" class="w-12 h-8 text-center border-x focus:outline-none" readonly>
                            <button type="button" onclick="incrementModalQuantity()" class="flex items-center justify-center w-8 h-8 hover:bg-gray-100">+</button>
                        </div>
                        <button onclick="addToCartWithVariant()" class="flex-1 btn-primary">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedProduct = null;
        let selectedVariant = null;

        function showVariantModal(product, variants) {
            selectedProduct = product;
            selectedVariant = null;
            document.getElementById('modalProductName').textContent = product.name;
            const variantsList = document.getElementById('variantsList');
            variantsList.innerHTML = variants.map(variant => `
                <button onclick="selectModalVariant(${variant.id})"
                        class="p-4 text-left border border-gray-200 rounded-lg variant-button hover:border-primary"
                        data-variant-id="${variant.id}">
                    <div class="font-medium">${variant.name}</div>
                    <div class="text-sm text-gray-500">Rp ${formatRupiah(variant.price)}</div>
                </button>
            `).join('');
            document.getElementById('modalQuantity').value = 1;
            document.getElementById('variantModal').classList.remove('hidden');
        }

        function closeVariantModal() {
            document.getElementById('variantModal').classList.add('hidden');
            selectedProduct = null;
            selectedVariant = null;
        }

        function selectModalVariant(variantId) {
            selectedVariant = variantId;
            document.querySelectorAll('.variant-button').forEach(btn => {
                btn.classList.remove('border-primary');
                btn.classList.add('border-gray-200');
            });
            document.querySelector(`[data-variant-id="${variantId}"]`).classList.add('border-primary');
        }

        function incrementModalQuantity() {
            const input = document.getElementById('modalQuantity');
            input.value = parseInt(input.value) + 1;
        }

        function decrementModalQuantity() {
            const input = document.getElementById('modalQuantity');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        function addToCartWithVariant() {
            if (!selectedVariant) {
                showPopup(`Silakan pilih varian untuk produk "${selectedProduct.name}"`, 'error');
                return;
            }

            const quantity = document.getElementById('modalQuantity').value;

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: selectedProduct.id,
                    variant_id: selectedVariant,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    if (data.error === 'out_of_stock') {
                        showPopup(`Maaf, stok varian yang dipilih sedang kosong`, 'error');
                    } else if (data.error === 'insufficient_stock') {
                        showPopup(`Stok varian yang dipilih tidak mencukupi`, 'error');
                    } else {
                        showPopup(data.message || `Gagal menambahkan "${selectedProduct.name}" ke keranjang`, 'error');
                    }
                    return;
                }

                updateCartCount(data.cart_count);
                document.getElementById('cart-dropdown').innerHTML = data.cartHtml;
                showPopup(`"${selectedProduct.name}" berhasil ditambahkan ke keranjang!`);
                closeVariantModal();
            })
            .catch(error => {
                console.error('Error:', error);
                showPopup(`Terjadi kesalahan saat menambahkan "${selectedProduct.name}" ke keranjang`, 'error');
            });
        }

        function addToCart(product, hasVariants) {
            if (hasVariants) {
                // Fetch variants data and show modal
                fetch(`/api/products/${product.id}/variants`)
                    .then(response => response.json())
                    .then(variants => {
                        if (variants.length === 0) {
                            showPopup(`Produk "${product.name}" tidak memiliki varian yang tersedia`, 'error');
                            return;
                        }
                        showVariantModal(product, variants);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showPopup(`Gagal memuat varian untuk produk "${product.name}"`, 'error');
                    });
                return;
            }

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: product.id,
                    variant_id: null,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    if (data.error === 'variant_required') {
                        // Jika error karena butuh varian, langsung fetch dan tampilkan varian
                        fetch(`/api/products/${product.id}/variants`)
                            .then(response => response.json())
                            .then(variants => showVariantModal(product, variants));
                        showPopup(`Silakan pilih varian untuk produk "${product.name}"`, 'error');
                    } else if (data.error === 'out_of_stock') {
                        showPopup(`Maaf, stok produk "${product.name}" sedang kosong`, 'error');
                    } else if (data.error === 'insufficient_stock') {
                        showPopup(`Stok produk "${product.name}" tidak mencukupi`, 'error');
                    } else {
                        showPopup(data.message || `Gagal menambahkan "${product.name}" ke keranjang`, 'error');
                    }
                    return;
                }

                updateCartCount(data.cart_count);
                document.getElementById('cart-dropdown').innerHTML = data.cartHtml;
                showPopup(`"${product.name}" berhasil ditambahkan ke keranjang!`);
            })
            .catch(error => {
                console.error('Error:', error);
                showPopup(`Terjadi kesalahan saat menambahkan "${product.name}" ke keranjang`, 'error');
            });
        }

        function showPopup(message, type = 'success') {
            const popup = document.getElementById('popup');
            const popupMessage = document.getElementById('popup-message');
            const popupIcon = document.getElementById('popup-icon');

            popupMessage.textContent = message;

            // Set icon based on type
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

            // Auto close after 3 seconds
            setTimeout(() => {
                closePopup();
            }, 3000);
        }

        function closePopup() {
            document.getElementById('popup').classList.add('hidden');
        }
    </script>

    <div class="container px-4 py-8 mx-auto">
            <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="/" class="text-gray-500 hover:text-primary">Beranda</a></li>
            <li class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-primary">Produk</a>
            </li>
            {{-- <li class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="text-gray-500">{{ $product->name }}</span>
            </li> --}}
        </ol>
    </nav>

    <!-- Product Listing -->
        @if($selectedCategory)
            <div class="mb-8">
                <h1 class="text-3xl font-bold">{{ $selectedCategory->name }}</h1>
                <p class="mt-2 text-gray-600">Menampilkan produk dalam kategori {{ $selectedCategory->name }}</p>
            </div>
        @endif

        <div class="grid grid-cols-12 gap-8">
            <!-- Sidebar Filters -->
            <div class="col-span-3">
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="mb-4 text-lg font-semibold">Filter</h3>

                    <form id="filter-form" class="mb-4" onsubmit="return false;">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium">Cari Produk</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-primary focus:border-primary"
                                placeholder="Nama produk..." onkeyup="debounce(loadProducts, 500)()">
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium">Kategori</label>
                            <select name="category" class="w-full px-3 py-2 border rounded-lg focus:ring-primary focus:border-primary" onchange="loadProducts()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium">Urutkan</label>
                            <select name="sort" class="w-full px-3 py-2 border rounded-lg focus:ring-primary focus:border-primary" onchange="loadProducts()">
                                <option value="">Pilih Urutan</option>
                                <option value="price_asc">Harga: Rendah ke Tinggi</option>
                                <option value="price_desc">Harga: Tinggi ke Rendah</option>
                                <option value="newest">Terbaru</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-span-9">
                <div class="grid grid-cols-3 gap-6">
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
                </div>

                <div id="loading-indicator" class="hidden">
                    <div class="flex items-center justify-center w-full p-8">
                        <div class="w-8 h-8 border-4 border-gray-300 rounded-full animate-spin border-t-primary"></div>
                    </div>
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function loadProducts(page = 1) {
            const form = document.getElementById('filter-form');
            const productsGrid = document.querySelector('.grid.grid-cols-3');
            const loadingIndicator = document.getElementById('loading-indicator');

            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            productsGrid.classList.add('opacity-50');

            // Build query string from form data
            const formData = new FormData(form);
            formData.append('page', page);
            const queryString = new URLSearchParams(formData).toString();

            // Update URL without reloading
            window.history.pushState({}, '', `${window.location.pathname}?${queryString}`);

            // Fetch products
            fetch(`/products?${queryString}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                productsGrid.innerHTML = html;
                initPagination();
            })
            .finally(() => {
                loadingIndicator.classList.add('hidden');
                productsGrid.classList.remove('opacity-50');
            });
        }

        function initPagination() {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = new URL(e.target.href).searchParams.get('page');
                    loadProducts(page);
                });
            });
        }

        // Initialize pagination when page loads
        document.addEventListener('DOMContentLoaded', initPagination);
    </script>
</x-app-layout>
