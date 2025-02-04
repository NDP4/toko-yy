<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Orders
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="mb-6 text-2xl font-semibold">Daftar Pesanan</h2>

                    @if($orders->isEmpty())
                        <p class="text-gray-500">Belum ada pesanan.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="text-left bg-gray-50">
                                        <th class="p-4">No. Pesanan</th>
                                        <th class="p-4">Tanggal</th>
                                        <th class="p-4">Total</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr class="border-t">
                                            <td class="p-4">{{ $order->order_number }}</td>
                                            <td class="p-4">{{ $order->created_at->format('d M Y') }}</td>
                                            <td class="p-4">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                            <td class="p-4">
                                                <span class="px-3 py-1 text-sm rounded-full
                                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="p-4">
                                                <a href="#" class="text-blue-600 hover:text-blue-800">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
