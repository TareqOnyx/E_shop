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
        Schema::create('payments', function (Blueprint $table) {
                  $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // المستخدم
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->unsignedBigInteger('payment_way_id'); // طريقة الدفع
            $table->decimal('amount', 10, 2); // المبلغ
            $table->string('status')->default('pending'); // حالة الدفع
            $table->string('transaction_id')->nullable(); // رقم العملية البنكية
            $table->timestamps();

            // العلاقات
            $table->foreign('payment_way_id')->references('id')->on('payment_ways')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
