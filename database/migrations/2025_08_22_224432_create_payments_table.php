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
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->unsignedBigInteger('payment_way_id'); 
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); 
            $table->string('transaction_id')->nullable(); 
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
