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
            $table->id();
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->string('po_number', 100)->unique();
            $table->date('po_date')->nullable();
            $table->string('po_payment_term', 50)->nullable();
            $table->string('wip_status', 50)->nullable();
            $table->date('exact_delivery_date')->nullable();
            $table->string('dimension', 255)->nullable();
            $table->string('weight', 100)->nullable();
            $table->text('shipping_documents')->nullable();
            $table->string('incoterm', 50)->nullable();
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
