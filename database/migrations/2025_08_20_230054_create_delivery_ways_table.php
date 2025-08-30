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
        Schema::create('delivery_ways', function (Blueprint $table) {
             $table->id();
        $table->string('name'); // اسم طريقة التوصيل (مثلاً: DHL, Aramex, Local Courier)
        $table->decimal('price', 8, 2); // تكلفة التوصيل
        $table->integer('estimated_days'); // المدة المتوقعة للتوصيل (بالأيام)
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_ways');
    }
};
