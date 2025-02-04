<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Toko YY') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        {{-- @if (isset($header))
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif --}}

        <main>
            {{ $slot }}
        </main>

        @include('layouts.footer')
    </div>

    {{-- <!-- Toast Notification -->
    <div id="toast" class="fixed z-50 transition-all duration-300 transform translate-x-full top-4 right-4">
        <div class="flex items-center px-6 py-3 space-x-2 text-white rounded-lg shadow-lg bg-primary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="toast-message"></span>
        </div>
    </div> --}}

    <script>
        function showToast(message, duration = 3000) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');

            toastMessage.textContent = message;
            toast.classList.remove('translate-x-full');

            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, duration);
        }

        function updateCartCount(count) {
            const cartCount = document.getElementById('cart-count');
            const cartWrapper = document.getElementById('cart-count-wrapper');

            if (count > 0) {
                if (cartCount) {
                    cartCount.textContent = count;
                } else {
                    const newCartCount = document.createElement('span');
                    newCartCount.id = 'cart-count';
                    newCartCount.className = 'absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs text-white transform translate-x-1/2 -translate-y-1/2 rounded-full bg-primary';
                    newCartCount.textContent = count;
                    cartWrapper.appendChild(newCartCount);
                }
            }
        }
    </script>
</body>
</html>
