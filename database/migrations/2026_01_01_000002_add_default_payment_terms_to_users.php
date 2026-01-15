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
        Schema::table('users', function (Blueprint $table) {
            $table->string('default_payment_terms', 10)->nullable()->default('30')->after('company_iban');
            $table->string('invoice_prefix', 10)->nullable()->default('FAC')->after('default_payment_terms');
            $table->string('quote_prefix', 10)->nullable()->default('OFF')->after('invoice_prefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['default_payment_terms', 'invoice_prefix', 'quote_prefix']);
        });
    }
};
