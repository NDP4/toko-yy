<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Toko YY</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Left Side - Image -->
        <div class="hidden bg-center bg-cover lg:flex lg:w-1/2" style="background-image: url('{{ asset('images/2024-03-08.jpg') }}')">
            <div class="flex items-center justify-center w-full bg-text/50">
                <div class="p-12 text-center text-white">
                    <h1 class="mb-4 text-5xl font-bold">Selamat Datang</h1>
                    <p class="text-xl">Toko Plastik & Bahan Roti Terlengkap</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex items-center justify-center w-full p-8 lg:w-1/2">
            <div class="w-full max-w-md">
                <div class="mb-10 text-center">
                    <a href="/" class="inline-block mb-6">
                        <img src="{{ asset('images/android-chrome-192x192.png') }}" alt="Toko YY Logo" class="w-20 h-20 mx-auto">
                    </a>
                    <h2 class="mb-2 text-4xl font-bold text-gray-900">Masuk ke Akun Anda</h2>
                    <p class="text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-primary hover:text-primary/80">Daftar disini</a></p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <div class="relative">
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-3 text-gray-900 border-gray-200 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                placeholder="Email">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </span>
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-3 text-gray-900 border-gray-200 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                placeholder="Password">
                            <button type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                    @error('g-recaptcha-response')
                        <p class="text-sm text-center text-red-500">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full px-4 py-3 font-medium text-white transition-colors duration-200 rounded-lg bg-primary hover:bg-primary/90">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 text-sm text-center text-gray-600">
                    <a href="#" class="text-primary hover:text-primary/80">Lupa password?</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
