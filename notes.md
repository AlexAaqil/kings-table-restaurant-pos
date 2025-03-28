# Constants

USERLEVELS = [
0 => 'super_admin',
1 => 'admin',
2 => 'cashier',
3 => 'customer',
];

# DB Design

users {
$table->id();
$table->string('first_name');
$table->string('last_name');
$table->string('email')->unique();
$table->string('phone_number');
$table->unsignedTinyInteger('user_level')->default(3);
$table->boolean('user_status')->default(1);
$table->string('password');
$table->timestamp('email_verified_at')->nullable();
$table->rememberToken();
$table->timestamps();
}

product_categories {
$table->id();
$table->string('title')->unique();
$table->string('slug')->index();
}

products {
$table->id();
$table->string('title')->unique();
$table->string('slug')->index();
$table->unsignedSmallInteger('product_code')->default(0);
$table->boolean('is_visible')->default(1);
$table->decimal('buying_price', 10, 2)->default(0.00);
$table->decimal('selling_price', 10, 2)->default(0.00);
$table->decimal('discount_price', 10, 2)->default(0.00)->nullable();
$table->string('product_measurement')->nullable();
$table->unsignedSmallInteger('product_order')->default(200);
$table->unsignedSmallInteger('stock_count')->default(0);
$table->unsignedSmallInteger('safety_stock')->default(0);
$table->text('description')->nullable();
$table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
$table->timestamps();
}

product_images {
$table->id();
$table->foreignId('product_id')->constrained('products');
$table->string('image');
$table->smallInteger('image_order')->default(5);
$table->timestamps();
}

sales {
$table->id();
$table->string('order_number');
$table->boolean('order_type')->default(true);
$table->string('discount_code')->nullable();
$table->decimal('discount',10,2)->default(0.00);
$table->decimal('total_amount', 10,2)->default(0.00);
$table->decimal('amount_paid', 10,2)->default(0.00);
$table->string('payment_method')->nullable();
$table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
$table->timestamps();
}

sales_items {
$table->id();
$table->foreignId('order_id')->constrained('sales')->onDelete('cascade');
$table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
$table->string('title');
$table->unsignedSmallInteger('quantity')->default(1);
$table->decimal('buying_price',10,2)->default(0);
$table->decimal('selling_price',10,2)->default(0);
$table->timestamps();
}

payments {
$table->id();
$table->string('status');
$table->string('payment_gateway');
$table->string('merchant_request_id');
$table->string('checkout_request_id');
$table->string('transaction_reference');
$table->string('response_code');
$table->string('response_description');
$table->text('customer_message');
$table->foreignId('order_id')->constrained('sales')->onDelete('cascade');
$table->timestamps();
}

# User Journeys
## Admin

- Can CRUD users.

- Can CRUD product categories.
- Can CRUD products.

- Can CRUD sales.

## Cashier

- Can R sales.
- Can P sales receipt.




# TODOs
<!-- - cashiers dashboard. -->
<!-- - sales page should have sales for today, yesterday and this week. -->
<!-- - improve the logic for printing receipts. -->


- list products grouped in categories.
- payment transaction reports searchable by transaction code.

Cashiers:
- shifts for cashiers.
- split order to pay with more than one payment method like mpesa and cash.
- start with a receipt for unpaid orders.
- receipt for paid orders.



Admins:
- Admins can delete pending orders.
- filter for transactions according to cashiers by day, month.
- sales reports with a filter option by total, date and time and cashier name.
- calculate total for each payment method.
- have an option to enter the commission a cashier can be paid with according to their daily sales.





{{-- <div class="table list_items">
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
                    </div> --}}