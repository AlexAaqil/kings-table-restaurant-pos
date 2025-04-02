<x-authenticated-layout>
    <x-slot name="head">
        <title>Shop</title>
    </x-slot>

    <section class="POSShop Products">
        <div class="row_container">
            <!-- Products Section -->
            <div class="column">
                @if ($categories->isNotEmpty())
                    <div class="table">
                        <div class="header">
                            <div class="details">
                                <p class="title">Products</p>
                                <p class="stats">
                                    <span>{{ $count_products }} {{ Str::plural('Product', $count_products) }}</span>
                                </p>
                            </div>
        
                            <x-search-input />

                            <div class="btn">
                                <a href="{{ route('product-categories.create') }}">New Category</a>
                            </div>
                        </div>
                        
                        @foreach($categories as $category)
                            <div class="categorized_products searchable">
                                <div class="category_header">
                                    <p class="title">{{ $category->name }}</p>
                                </div>

                                <div class="cards">
                                    @forelse($category->products as $product)
                                        <div class="card searchable {{ $product->is_visible ? '' : 'danger_border' }}" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->getEffectivePrice() }}">
                                            <div class="image">
                                                <img src="{{ $product->getFirstImage() ?? asset('assets/images/default_image.jpg') }}" alt="Product Image" width="80" height="80">
                                            </div>

                                            <div class="details">
                                                <p class="title">{{ $product->name }}</p>
                                                <p class="prices">
                                                    <span class="price success">Ksh. {{ number_format($product->getEffectivePrice(), 2) }}</span>
                                                </p>
                                                <p class="category">{{ $product->category->name ?? 'uncategorized' }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="no-products">No products in this category.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No products available.</p>
                @endif
            </div>

            <!-- Cart Section -->
            <div class="cart_items">
                <div class="cart">
                    <div class="cart_header">
                        <p>Cart</p>
                    </div>

                    <div class="cart_content">
                        <!-- Cart items will be dynamically added here -->
                    </div>

                    <div class="cart_footer">
                        <p><b>Total: Ksh. <span id="cartTotal">0.00</span></b></p>

                        <button id="generateOrderButton">Generate Order</button>
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
                                <button class="remove">X</button>
                            </div>
                        `;
                        cartContent.innerHTML += cartItem;
                    });

                    document.getElementById("cartTotal").innerText = total.toFixed(2);

                    // Pre-fill "Amount Paid" field
                    document.getElementById("amountPaid").value = total.toFixed(2);

                    // Update change field dynamically
                    updateChangeGiven();
                }

                // Add products to cart
                document.querySelectorAll(".card").forEach(product => {
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

                // Update quantity or remove items
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

                document.getElementById("generateOrderButton").addEventListener("click", function () {
                    if (Object.keys(cart).length === 0) {
                        alert("Cart is empty!");
                        return;
                    }

                    let saleData = {
                        items: Object.values(cart),
                        total_amount: parseFloat(document.getElementById("cartTotal").innerText),
                    };

                    fetch("/sales/store", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(saleData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert("Order receipt generated!");
                        window.location.href = `/receipt/${data.sale_id}`;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Failed to generate order receipt!");
                    });
                });
            });
        </script>
    </x-slot>
</x-authenticated-layout>
