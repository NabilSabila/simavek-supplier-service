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
    Schema::create('purchase_orders', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('po_number')->unique();
        $table->uuid('supplier_id');
        $table->uuid('medicine_id');
        $table->integer('quantity');
        $table->decimal('price', 12, 2);
        $table->enum('status', ['pending', 'approved', 'received', 'canceled']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
