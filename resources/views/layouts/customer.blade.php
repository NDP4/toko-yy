<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    {{-- <nav class="bg-white shadow-sm">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-2">
                    <img src="{{ asset('images/android-chrome-192x192.png') }}" alt="Logo" class="w-8 h-8">
                    <span class="text-xl font-bold text-primary">Toko YY</span>
                </a>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </nav> --}}

    <!-- Main Content -->
    <div class="container min-h-screen px-4 py-8 mx-auto">
        <div class="grid grid-cols-12 gap-6">
            <!-- Sidebar -->
            <div class="col-span-12 md:col-span-3">
                <div class="p-4 bg-white rounded-lg shadow">
                    <nav class="space-y-1">
                        <a href="{{ route('customer.dashboard') }}"
                           class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('customer.dashboard') ? 'bg-primary text-white' : 'hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('customer.profile') }}"
                           class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('customer.profile') ? 'bg-primary text-white' : 'hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('customer.orders') }}"
                           class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('customer.orders*') ? 'bg-primary text-white' : 'hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Pesanan Saya
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Content -->
            <div class="col-span-12 md:col-span-9">
                <div class="p-6 bg-white rounded-lg shadow">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
