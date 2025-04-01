# User Journeys
## Admin
- can login.
- can CRUD users.

- can view dashboard with sales summary (sales this month, last week, this week, yesterday, today).
- can view dashboard with payment summary (mpesa sales, cash sales).
- can view dashboard with sales according to cashiers shifts.

- can CRUD product categories.
- can CRUD products.

- can CRUD sales.
- can P sales receipts for paid and unpaid order.
- can filter sales according to cashier, date and time.
- can search payment according to amount or transaction code then link to an order.

- can enter commissions to pay cashiers according to daily sales.

## Cashier
- can login.
- can keep track or when the shift starts or ends.

- can view dashboard with sales summary.
- can view dashboard with payment summary (mpesa sales, cash sales).

- can view categorized products on the shop page.

- can R sales.
- can P sales receipt for paid and unpaid orders.
- can split orders between two people or merge to become for one person.
- can split payments to show payments made by cash or mpesa or card.
- can search payment according to amount or transaction code then link to an order.



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

work_shifts {
    $table->id();
    $table->datetime('shift_start')->nullable();
    $table->datetime('shift_end')->nullable();
    $table->string('status')->default('active');
    $table->decimal('total_sales_amount', 10, 2)->default(0.00);
    $table->decimal('total_commission', 10, 2)->default(0.00);

    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
    $table->unsignedSmallInteger('product_ordering')->default(200);
    $table->unsignedSmallInteger('stock_count')->default(0);
    $table->unsignedSmallInteger('safety_stock')->default(0);

    $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
    $table->timestamps();
}

product_images {
    $table->id();
    $table->string('image');
    $table->smallInteger('image_order')->default(5);

    $table->foreignId('product_id')->constrained('products');
    $table->timestamps();
}

stock_movements {
    $table->id();
    $table->string('reference_number')->nullable();
    $table->string('movement_type');
    $table->unsignedSmallInteger('quantity');

    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
}

sales {
    $table->id();
    $table->string('order_number')->unique();
    $table->unsignedTinyInteger('order_type')->default(0);
    $table->unsignedTinyInteger('status')->default(0);
    $table->string('discount_code')->nullable();
    $table->decimal('discount',10,2)->default(0.00);
    $table->decimal('total_amount', 10,2)->default(0.00);
    $table->decimal('amount_paid', 10,2)->default(0.00);

    $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
}

sales_items {
    $table->id();
    $table->string('title');
    $table->unsignedSmallInteger('quantity')->default(1);
    $table->decimal('buying_price',10,2)->default(0);
    $table->decimal('selling_price',10,2)->default(0);

    $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
    $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
    $table->timestamps();
}

sales_payments {
    $table->id();
    $table->decimal('amount_paid', 10, 2);
    $table->string('payment_method');
    $table->string('transaction_reference')->nullable();
    $table->timestamp('payment_date')->default(now());

    $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
    $table->timestamps();
}

payments {
    $table->id();
    $table->string('status');
    $table->decimal('amount_paid', 10, 2);
    $table->string('payment_gateway');
    $table->string('merchant_request_id')->nullable();
    $table->string('checkout_request_id')->nullable();
    $table->string('transaction_reference')->nullable();
    $table->string('response_code')->nullable();
    $table->string('response_description')->nullable();
    $table->text('customer_message')->nullable();

    $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
    $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
}

settings {
    $table->id();
    $table->string('organization_name');
    $table->string('location');
    $table->string('phone_number');
    $table->string('other_phone_number')->nullable();
    $table->string('email');
    $table->string('currency')->default('KES');
    $table->json('commission_tiers')->nullable();
}



# Constants
USERLEVELS = [
    0 => 'super_admin',
    1 => 'admin',
    2 => 'cashier',
    3 => 'customer',
];

SHIFTSTATUS = [
    'active',
    'closed',
    'paused',
];

SALESTYPE = [
    0 => 'walk-in',
    1 => 'online',
];

SALESSTATUSTYPE = [
    0 => 'pending',
    1 => 'completed',
    2 => 'canceled',
    3 => 'refund',
];

PAYMENTMETHODS = [
    'cash',
    'mpesa',
    'card',
    'bank_transfer',
];

PAYMENTSTATUSTYPE = [
    'pending',
    'confirmed',
    'failed',
    'reversed',
];

STOCKMOVEMENTYPE = [
    'purchase', 
    'sale', 
    'restock', 
    'adjustment',
];
