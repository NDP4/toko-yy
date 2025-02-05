<x-app-layout>
    <div class="container py-8 mx-auto">
        <h1 class="mb-6 text-2xl font-bold">Checkout</h1>

        <div class="grid grid-cols-12 gap-8">
            <!-- Form Pengiriman -->
            <div class="col-span-8 space-y-6">
                <div class="p-6 bg-white rounded-lg shadow">
                    <h2 class="mb-4 text-lg font-semibold">Alamat Pengiriman</h2>

                    <!-- Toggle Alamat -->
                    <div class="mb-4 space-y-4">
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="useDefaultAddress" name="addressType" value="default" checked
                                   class="text-primary focus:ring-primary">
                            <label for="useDefaultAddress">Gunakan alamat akun</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="useNewAddress" name="addressType" value="new"
                                   class="text-primary focus:ring-primary">
                            <label for="useNewAddress">Gunakan alamat lain</label>
                        </div>
                    </div>

                    <!-- Form Alamat -->
                    <form id="shippingForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-sm">Nama Penerima</label>
                                <input type="text" id="receiverName" class="w-full input-field"
                                       value="{{ auth()->user()->name }}">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm">Nomor Telepon</label>
                                <input type="tel" id="phone" class="w-full input-field"
                                       value="{{ auth()->user()->phone }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-sm">Provinsi</label>
                                <select id="province" class="w-full input-field" onchange="getCities()">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province['province_id'] }}">
                                            {{ $province['province'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm">Kota/Kabupaten</label>
                                <select id="city" class="w-full input-field" onchange="calculateShipping()" disabled>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm">Kode Pos</label>
                            <input type="text" id="postal_code" class="w-full input-field">
                        </div>

                        <div>
                            <label class="block mb-1 text-sm">Alamat Lengkap</label>
                            <textarea id="address" rows="3" class="w-full input-field">{{ auth()->user()->address }}</textarea>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm">Kurir</label>
                            <select id="courier" class="w-full input-field" onchange="calculateShipping()">
                                <option value="">Pilih Kurir</option>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>

                        <div id="shippingServices" class="space-y-2">
                            <!-- Shipping services will be loaded here -->
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ringkasan Pesanan -->
            <div class="col-span-4">
                <div class="sticky p-6 bg-white rounded-lg shadow top-4">
                    <h2 class="mb-4 text-lg font-semibold">Ringkasan Pesanan</h2>

                    <!-- Daftar Produk -->
                    <div class="space-y-4">
                        @foreach($cart->items as $item)
                            <div class="flex space-x-4">
                                <img src="{{ Storage::url($item->product->primary_image) }}"
                                     class="object-cover w-16 h-16 rounded"
                                     alt="{{ $item->product->name }}">
                                <div class="flex-1">
                                    <h4 class="font-medium">{{ $item->product->name }}</h4>
                                    @if($item->variant)
                                        <p class="text-sm text-gray-500">{{ $item->variant->name }}</p>
                                    @endif
                                    <div class="flex justify-between mt-1">
                                        <span class="text-sm">{{ $item->quantity }}x</span>
                                        <span class="font-medium">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Rincian Pembayaran -->
                    <div class="pt-4 mt-4 space-y-2 border-t">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Berat</span>
                            <span>{{ number_format($cart->items->sum(function($item) {
                                return $item->quantity * $item->product->weight;
                            }) / 1000, 1) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span id="shippingCost">Rp 0</span>
                        </div>
                        <div class="flex justify-between pt-2 text-lg font-bold border-t">
                            <span>Total</span>
                            <span class="text-primary" id="grandTotal">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button onclick="processCheckout()" class="w-full mt-6 btn-primary">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedShippingCost = 0;

        function getCities() {
            const provinceId = document.getElementById('province').value;
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');
            const postalCodeInput = document.getElementById('postal_code');

            if (!citySelect) return;

            citySelect.disabled = true;
            if (districtSelect) districtSelect.disabled = true;
            if (postalCodeInput) postalCodeInput.value = '';

            if (!provinceId) {
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                return;
            }

            citySelect.innerHTML = '<option value="">Memuat...</option>';

            fetch(`/checkout/cities/${provinceId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data && result.data.length > 0) {
                        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                        result.data.forEach(city => {
                            citySelect.innerHTML += `<option value="${city.city_id}">${city.type} ${city.city_name}</option>`;
                        });
                    } else {
                        citySelect.innerHTML = '<option value="">Kota tidak tersedia</option>';
                    }
                    citySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    citySelect.innerHTML = '<option value="">Error memuat data</option>';
                });
        }

        function calculateShipping() {
            const cityId = document.getElementById('city').value;
            const courier = document.getElementById('courier').value;
            const servicesDiv = document.getElementById('shippingServices');

            if (!cityId || !courier || !servicesDiv) return;

            const totalWeight = {{ $cart->items->sum(function($item) {
                return $item->quantity * $item->product->weight;
            }) }};

            servicesDiv.innerHTML = '<p class="text-sm text-gray-500">Memuat layanan pengiriman...</p>';

            fetch('/checkout/shipping-cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    destination: cityId,
                    weight: totalWeight,
                    courier: courier
                })
            })
            .then(response => response.json())
            .then(result => {
                console.log('Shipping cost response:', result); // Debug log
                if (result.success && result.data && result.data.length > 0) {
                    servicesDiv.innerHTML = result.data.map(service => `
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="shippingService"
                                   id="shipping_${service.service}"
                                   value="${service.cost[0].value}"
                                   onchange="updateTotal(${service.cost[0].value})"
                                   class="text-primary focus:ring-primary">
                            <label for="shipping_${service.service}" class="flex justify-between flex-1 text-sm">
                                <span>${service.service} (${service.description})</span>
                                <span>Rp ${new Intl.NumberFormat('id-ID').format(service.cost[0].value)}</span>
                            </label>
                        </div>
                    `).join('');
                } else {
                    servicesDiv.innerHTML = `<p class="text-sm text-red-500">${result.message || 'Layanan pengiriman tidak tersedia'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                servicesDiv.innerHTML = '<p class="text-sm text-red-500">Gagal memuat layanan pengiriman</p>';
            });
        }

        function updateTotal(shippingCost) {
            selectedShippingCost = shippingCost;
            const subtotal = {{ $cart->total }};
            const total = subtotal + shippingCost;

            document.getElementById('shippingCost').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(shippingCost)}`;
            document.getElementById('grandTotal').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        }

        // Handle address type toggle
        document.querySelectorAll('input[name="addressType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const useDefault = this.value === 'default';
                document.getElementById('receiverName').value = useDefault ? '{{ auth()->user()->name }}' : '';
                document.getElementById('phone').value = useDefault ? '{{ auth()->user()->phone }}' : '';
                document.getElementById('address').value = useDefault ? '{{ auth()->user()->address }}' : '';
            });
        });
    </script>
</x-app-layout>
