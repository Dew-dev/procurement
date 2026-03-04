<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_wip_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('percentage');
            $table->date('status_date')->nullable();
            $table->timestamps();

            $table->unique(['purchase_order_id', 'percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_wip_statuses');
    }
};
