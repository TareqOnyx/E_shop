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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // مرتبط بجدول orders
            $table->unsignedBigInteger('delivery_way_id'); // نوع التوصيل
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // حالة التوصيل
            $table->string('tracking_number')->nullable(); // رقم التتبع إذا متوفر
            $table->timestamps();

            // العلاقات
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('delivery_way_id')->references('id')->on('delivery_ways')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
