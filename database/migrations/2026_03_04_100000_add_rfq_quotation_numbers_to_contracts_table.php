<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('rfq_number', 100)->nullable()->after('rfq_from_buyer');
            $table->string('quotation_number', 100)->nullable()->after('quotation_to_buyer');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['rfq_number', 'quotation_number']);
        });
    }
};
