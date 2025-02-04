<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko YY - Toko Plastik & Bahan Roti</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

    <!-- Header -->
    @include('layouts.navigation')
    {{-- <header class="shadow bg-background">
        <nav class="container px-4 py-6 mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-12">
                    <a href="/" class="flex items-center space-x-2">
                        <img src="{{ asset('images/android-chrome-192x192.png') }}" alt="Toko YY Logo" class="h-12">
                        <span class="text-2xl font-bold text-primary">Toko YY</span>
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
    </header> --}}

    <!-- Hero Section -->
    <section class="py-16 bg-secondary/5">
        <div class="container px-4 mx-auto">
            <div class="grid items-center gap-12 md:grid-cols-2">
                <div>
                    <h1 class="mb-6 text-5xl font-bold text-text">Toko Plastik & Bahan Roti Terlengkap</h1>
                    <p class="mb-8 text-lg text-text/80">Temukan berbagai kebutuhan plastik dan bahan roti berkualitas dengan harga terbaik untuk bisnis Anda.</p>
                    <a href="/products" class="btn-primary">Mulai Belanja</a>
                </div>
                <div>
                    <img src="{{ asset('images/2024-03-08.jpg') }}" alt="Hero Image" class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16" id="categories">
        <div class="container px-4 mx-auto">
            <h2 class="mb-12 text-3xl font-bold text-center text-text">Kategori Produk</h2>
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->id]) }}"
                       class="p-6 text-center transition bg-white rounded-lg shadow-lg group hover:shadow-xl">
                        <img src="{{ Storage::url($category->icon) }}"
                             alt="{{ $category->name }}"
                             class="object-contain w-20 h-20 mx-auto mb-4">
                        <h3 class="text-lg font-semibold transition text-text group-hover:text-primary">
                            {{ $category->name }}
                        </h3>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-secondary/5" id="products">
        <div class="container px-4 mx-auto">
            <h2 class="mb-12 text-3xl font-bold text-center text-text">Produk Unggulan</h2>
            <div class="grid gap-8 md:grid-cols-4">
                @foreach($featuredProducts as $product)
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
                                <button onclick="addToCart({{ $product->id }})"
                                        class="p-2 text-white transition rounded-lg bg-primary hover:bg-primary/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('products.index') }}" class="btn-primary">
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-16">
        <div class="container px-4 mx-auto">
            <h2 class="mb-12 text-3xl font-bold text-center text-text">Produk Terbaru</h2>
            <div class="grid gap-8 md:grid-cols-4">
                @foreach($newArrivals as $product)
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
                                <button onclick="addToCart({{ $product->id }})"
                                        class="p-2 text-white transition rounded-lg bg-primary hover:bg-primary/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 text-white bg-secondary">
        <div class="container px-4 mx-auto">
            <div class="grid gap-8 md:grid-cols-4">
                <div class="col-span-1">
                    <h3 class="mb-4 text-xl font-bold">Toko YY</h3>
                    <p class="mb-2 text-white/80">Toko Plastik & Bahan Roti Terlengkap</p>
                    <div class="flex items-center mt-4 space-x-2 text-white/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Buka Setiap Hari: 8.00 AM - 5.30 PM</span>
                    </div>
                </div>
                <div class="col-span-2">
                    <h3 class="mb-4 text-xl font-bold">Hubungi Kami</h3>
                    <div class="space-y-2 text-white/80">
                        <p class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Ngimbrang_kedu No.4, RT.1/RW.1, Kauman, Kedu, Temanggung, Kabupaten Temanggung, Jawa Tengah 56252</span>
                        </p>
                        <p class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="https://wa.me/6285157949334?text=Halo%20Toko%20YY%2C%20saya%20menemukan%20ini%20dari%20website%20anda%20saya%20ingin%20bertanya%20......"
                               target="_blank"
                               class="flex items-center space-x-2 hover:text-primary">
                                <span>0851-5794-9334</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                            </a>
                        </p>
                    </div>
                </div>
                <div class="col-span-1">
                    <h3 class="mb-4 text-xl font-bold">Lokasi</h3>
                    <div class="w-full h-48 overflow-hidden rounded-lg">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6739271263896!2d110.15217249999999!3d-7.277893499999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7079cd967b5651%3A0xbaebb7e0fa297103!2sToko%20Plastik%20%26%20Bahan%20Roti%20YY!5e0!3m2!1sen!2sid!4v1738652755649!5m2!1sen!2sid"
                            class="w-full h-full"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
            <div class="pt-8 mt-8 text-center border-t border-white/20 text-white/60">
                <p>&copy; {{ date('Y') }} Toko YY. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
