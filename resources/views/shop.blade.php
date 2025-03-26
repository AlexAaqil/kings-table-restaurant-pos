<x-authenticated-layout>
    <x-slot name="head">
        <title>POS System</title>
    </x-slot>

    <section class="POSShop">
        <div class="row_container">
            <!-- Products Section -->
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
                                <div class="product searchable" 
                                     data-id="{{ $product->id }}" 
                                     data-name="{{ $product->name }}" 
                                     data-price="{{ $product->getEffectivePrice() }}">
                                    <div class="image">
                                        <img src="{{ $product->getFirstImage() ?? asset('assets/images/default_image.jpg') }}" 
                                             alt="Product Image" width="80" height="80">
                                    </div>

                                    <div class="details">
                                        <p class="title">{{ $product->name }}</p>
                                        {{-- <p class="code">Code: {{ $product->code ?? '-' }}</p> --}}
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

                            <div class="input">
                                <label>Change Given:</label>
                                <input type="text" id="changeGiven" readonly>
                            </div>
                        </div>

                        <button id="checkoutButton">Checkout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Printable Receipt Section -->
        {{-- <div class="receipt hidden">
            <h2>Receipt</h2>
            <div class="receipt_content">
                <p><b>Date:</b> <span id="receiptDate"></span></p>
                <p><b>Payment Method:</b> <span id="receiptPaymentMethod"></span></p>
                <p><b>Total:</b> Ksh. <span id="receiptTotal"></span></p>
                <p><b>Amount Paid:</b> Ksh. <span id="receiptPaid"></span></p>
                <p><b>Change Given:</b> Ksh. <span id="receiptChange"></span></p>
            </div>
            <button id="printReceipt">Print Receipt</button>
        </div> --}}
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

                function updateChangeGiven() {
                    let amountPaid = parseFloat(document.getElementById("amountPaid").value) || 0;
                    let total = parseFloat(document.getElementById("cartTotal").innerText) || 0;
                    let change = (amountPaid - total).toFixed(2);

                    document.getElementById("changeGiven").value = change >= 0 ? change : "Insufficient amount";
                }

                // Add products to cart
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

                // Handle amount paid input and change given
                document.getElementById("amountPaid").addEventListener("input", updateChangeGiven);

                // Handle checkout
                document.getElementById("checkoutButton").addEventListener("click", function () {
                    let enteredAmount = parseFloat(document.getElementById("amountPaid").value);
                    let total = parseFloat(document.getElementById("cartTotal").innerText);
                    let paymentMethod = document.getElementById("paymentMethod").value;

                    if (Object.keys(cart).length === 0) {
                        alert("Cart is empty!");
                        return;
                    }

                    if (!enteredAmount || enteredAmount < total) {
                        alert("Amount paid is less than the total cost!");
                        return;
                    }

                    // Ask for confirmation on the actual amount paid
                    let confirmedAmount = prompt(`Confirm the actual amount paid (Total: Ksh. ${total.toFixed(2)}):`, enteredAmount);
                    
                    if (!confirmedAmount || isNaN(confirmedAmount) || parseFloat(confirmedAmount) < total) {
                        alert("Invalid or insufficient amount entered. Please enter the correct amount.");
                        return;
                    }

                    confirmedAmount = parseFloat(confirmedAmount);

                    let saleData = {
                        items: Object.values(cart),
                        total_amount: total,
                        amount_paid: confirmedAmount,
                        payment_method: paymentMethod
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
                        alert("Sale completed successfully! Change Given: Ksh. " + data.change);

                        // Clear cart and fields
                        cart = {};
                        updateCartUI();
                        document.getElementById("amountPaid").value = "";
                        document.getElementById("changeGiven").value = "";
                        
                        // Redirect to receipt page
                        window.location.href = `/receipt/${data.sale_id}`;
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
