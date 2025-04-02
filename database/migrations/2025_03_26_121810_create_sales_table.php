<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_reference')->unique();
            $table->unsignedTinyInteger('sale_type')->default(0);
            $table->unsignedTinyInteger('sale_status')->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('discount',10,2)->default(0.00);
            $table->decimal('total_amount', 10,2)->default(0.00);
            $table->decimal('amount_paid', 10,2)->default(0.00);

            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
