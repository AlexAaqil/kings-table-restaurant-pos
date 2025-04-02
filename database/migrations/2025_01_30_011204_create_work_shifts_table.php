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
        Schema::create('work_shifts', function (Blueprint $table) {
            $table->id();
            $table->datetime('shift_start')->nullable();
            $table->datetime('shift_end')->nullable();
            $table->string('status')->default('active');
            $table->decimal('total_sales_amount', 10, 2)->default(0.00);
            $table->decimal('total_commission', 10, 2)->default(0.00);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works_shifts');
    }
};
