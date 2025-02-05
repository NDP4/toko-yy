<x-app-layout>
    <div class="container py-8 mx-auto">
        <h1 class="mb-6 text-2xl font-bold">Keranjang Belanja</h1>
        <div id="cart-content">
            <!-- Cart content will be loaded here -->
        </div>
    </div>

    <script>
        function loadCart() {
            fetch('{{ route('cart.index') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('cart-content').innerHTML = html;
            });
        }

        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;

            fetch(`/cart/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCart();
                    if (typeof updateCartCount === 'function' && data.cart_count !== undefined) {
                        updateCartCount(data.cart_count);
                    }
                    if (data.cartHtml && document.getElementById('cart-dropdown')) {
                        document.getElementById('cart-dropdown').innerHTML = data.cartHtml;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadCart(); // Reload cart to ensure consistent state
            });
        }

        function removeItem(itemId) {
            fetch(`/cart/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCart();
                    if (typeof updateCartCount === 'function' && data.cart_count !== undefined) {
                        updateCartCount(data.cart_count);
                    }
                    if (data.cartHtml && document.getElementById('cart-dropdown')) {
                        document.getElementById('cart-dropdown').innerHTML = data.cartHtml;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadCart(); // Reload cart to ensure consistent state
            });
        }

        // Load cart on page load
        document.addEventListener('DOMContentLoaded', loadCart);
    </script>
</x-app-layout>
