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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('payment_id')->nullable();
        $table->unsignedBigInteger('delivery_id')->nullable();
        $table->decimal('total', 10, 2);
        $table->string('status')->default('pending'); // pending, confirmed, shipped, delivered, canceled
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
        $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
