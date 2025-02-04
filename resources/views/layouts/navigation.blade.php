<header class="shadow bg-background">
    <nav class="container px-4 py-6 mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-12">
                <a href="/" class="flex items-center space-x-2">
                    <img src="{{ asset('images/android-chrome-192x192.png') }}" alt="Toko YY Logo" class="h-12">
                    {{-- <span class="text-2xl font-bold text-primary">Toko YY</span> --}}
                </a>
                <div class="hidden space-x-8 md:flex">
                    <a href="/" class="transition text-text hover:text-primary">Beranda</a>
                    <a href="/products" class="transition text-text hover:text-primary">Produk</a>
                    <a href="#categories" class="transition text-text hover:text-primary">Kategori</a>
                    <a href="#about" class="transition text-text hover:text-primary">Tentang Kami</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" placeholder="Cari produk..." class="w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <!-- Cart Button -->
                <div class="relative group">
                    <a href="{{ route('cart.index') }}" class="relative flex items-center" id="cart-count-wrapper">
                        <button class="p-2 text-gray-600 transition hover:text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                        @php
                            try {
                                $cart = auth()->check()
                                    ? \App\Models\Cart::with(['items.product', 'items.variant'])->where('user_id', auth()->id())->first()
                                    : \App\Models\Cart::with(['items.product', 'items.variant'])->where('session_id', session()->get('cart_id'))->first();
                                $cartCount = $cart ? $cart->items->sum('quantity') : 0;
                            } catch (\Exception $e) {
                                $cart = null;
                                $cartCount = 0;
                            }
                        @endphp
                        @if($cartCount > 0)
                            <span id="cart-count" class="absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs text-white transform translate-x-1/2 -translate-y-1/2 rounded-full bg-primary">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Cart Dropdown -->
                    <div id="cart-dropdown" class="absolute right-0 hidden mt-0 bg-white rounded-lg shadow-xl w-80 group-hover:block">
                        @include('partials.cart-dropdown', ['cart' => $cart])
                    </div>
                </div>

                @auth
                    <div class="relative group">
                        <button class="flex items-center space-x-2 btn-secondary">
                            <span>{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute right-0 hidden w-48 py-2 mt-0 bg-white rounded-lg shadow-xl group-hover:block">
                            <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                            <a href="{{ route('customer.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pesanan</a>
                            <hr class="my-2 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-secondary">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
