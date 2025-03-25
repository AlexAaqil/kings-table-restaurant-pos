<x-authenticated-layout>
    <x-slot name="head">
        <title>Shop</title>
    </x-slot>

    <section class="POSShop">
        <div class="row_container">
            <div class="column">
                @if ($products->isNotEmpty())
                    <div class="table list_items">
                        <div class="header">
                            <div class="details">
                                <p class="title">Products</p>
                                <p class="stats">
                                    <span>{{ $count_products }} {{ Str::plural('Product', $count_products) }}</span>
                                </p>
                            </div>

                            <x-search-input />
                        </div>

                        <div class="product_list">
                            @foreach($products as $product)
                                <div class="product searchable" data-id="{{ $product->id }}" data-name="{{ $product->name }}" 
                                    data-price="{{ $product->getEffectivePrice() }}">
                                    <div class="image">
                                        <img src="{{ $product->getFirstImage() ?? asset('assets/images/default_image.jpg') }}" alt="Product Image" width="100" height="100">
                                    </div>

                                    <div class="details">
                                        <p class="title">{{ $product->name }}</p>
                                        <p class="code">Code: {{ $product->code ?? '-' }}</p>
                                        <p class="prices">
                                            <span class="price success">Ksh. {{ number_format($product->getEffectivePrice(), 2) }}</span>
                                        </p>
                                        <p class="category">{{ $product->category->name ?? 'uncategorized' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p>No product categories yet.</p>
                @endif
            </div>

            <div class="cart_items">
                <div class="cart">
                    <div class="cart_header">
                        <p>Cart</p>
                    </div>

                    <div class="cart_content">
                        <!-- Cart items will be added dynamically here -->
                    </div>

                    <div class="cart_footer">
                        <p><b>Total: Ksh. <span id="cartTotal">0.00</span></b></p>
                        <div class="inputs">
                            <div class="input">
                                <label>Amount Paid:</label>
                                <input type="number" id="amountPaid" placeholder="Enter amount paid">
                            </div>
                        
                            <div class="input">
                                <label>Payment Method:</label>
                                <select id="paymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                        </div>

                        <button id="checkoutButton">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let cart = {};

                function updateCartUI() {
                    const cartContent = document.querySelector(".cart_content");
                    cartContent.innerHTML = "";
                    let total = 0;

                    Object.values(cart).forEach(item => {
                        let itemTotal = item.price * item.quantity;
                        total += itemTotal;

                        let cartItem = `
                            <div class="cart_item" data-id="${item.id}">
                                <p class="item">${item.name}</p>
                                <p class="quantity_wrapper">
                                    <button class="decrease">-</button>
                                    <span class="quantity">${item.quantity}</span>
                                    <button class="increase">+</button>
                                </p>
                                <button class="remove"><i class="fas fa-trash-alt"></i> Delete</button>
                            </div>
                        `;
                        cartContent.innerHTML += cartItem;
                    });

                    document.getElementById("cartTotal").innerText = total.toFixed(2);
                }

                document.querySelectorAll(".product").forEach(product => {
                    product.addEventListener("click", function () {
                        let id = this.dataset.id;
                        let name = this.dataset.name;
                        let price = parseFloat(this.dataset.price);

                        if (!cart[id]) {
                            cart[id] = { id, name, price, quantity: 1 };
                        } else {
                            cart[id].quantity++;
                        }

                        updateCartUI();
                    });
                });

                document.querySelector(".cart_content").addEventListener("click", function (event) {
                    let itemElement = event.target.closest(".cart_item");
                    if (!itemElement) return;

                    let id = itemElement.dataset.id;

                    if (event.target.classList.contains("increase")) {
                        cart[id].quantity++;
                    } else if (event.target.classList.contains("decrease")) {
                        if (cart[id].quantity > 1) {
                            cart[id].quantity--;
                        } else {
                            delete cart[id];
                        }
                    } else if (event.target.classList.contains("remove")) {
                        delete cart[id];
                    }

                    updateCartUI();
                });

                document.getElementById("checkoutButton").addEventListener("click", function () {
                    let amountPaid = parseFloat(document.getElementById("amountPaid").value);
                    let paymentMethod = document.getElementById("paymentMethod").value;

                    if (Object.keys(cart).length === 0) {
                        alert("Cart is empty!");
                        return;
                    }

                    if (!amountPaid || amountPaid < 0) {
                        alert("Enter a valid amount paid.");
                        return;
                    }

                    let total = Object.values(cart).reduce((sum, item) => sum + item.price * item.quantity, 0);

                    if (amountPaid < total) {
                        alert("Amount paid is less than total cost!");
                        return;
                    }

                    let saleData = {
                        items: Object.values(cart),
                        total,
                        amountPaid,
                        paymentMethod
                    };

                    fetch("{{ route('sales.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(saleData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert("Sale completed successfully!");
                        cart = {};
                        updateCartUI();
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Sale failed!");
                    });
                });
            });
        </script>
    </x-slot>
</x-authenticated-layout>
