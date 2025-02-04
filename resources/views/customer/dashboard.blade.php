<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="mb-6 text-2xl font-semibold">Selamat Datang, {{ $user->name }}!</h2>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Orders Summary Card -->
                        <div class="p-6 bg-white border rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Total Pesanan</h3>
                                    <p class="text-3xl font-bold text-blue-600">{{ $user->orders()->count() }}</p>
                                </div>
                            </div>
                            <a href="{{ route('customer.orders') }}" class="inline-block mt-4 text-sm text-blue-600 hover:text-blue-800">
                                Lihat semua pesanan →
                            </a>
                        </div>

                        <!-- Profile Info Card -->
                        <div class="p-6 bg-white border rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Profil</h3>
                                    <p class="text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('customer.profile') }}" class="inline-block mt-4 text-sm text-green-600 hover:text-green-800">
                                Update profil →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
