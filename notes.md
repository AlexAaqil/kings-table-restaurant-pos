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

order_items {
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



## CSS RESPONSIVE DESIGN
// width >= 726px === min-width: 726px
// width <= 726px === max-width: 726px
/* Mobile-first approach (default styles for mobile) */

/* Small Phones (<= 480px) */
@media screen and (max-width: 480px) {
    body {
        font-size: 14px;
    }
}

/* Medium Phones (481px - 767px) */
@media screen and (min-width: 481px) and (max-width: 767px) {
    body {
        font-size: 16px;
    }
}

/* Tablets (768px - 1024px) */
@media screen and (min-width: 768px) and (max-width: 1024px) {
    body {
        font-size: 18px;
    }
}

/* Laptops (1025px - 1440px) */
@media screen and (min-width: 1025px) and (max-width: 1440px) {
    body {
        font-size: 20px;
    }
}

/* Desktops (1441px and above) */
@media screen and (min-width: 1441px) {
    body {
        font-size: 22px;
    }
}
