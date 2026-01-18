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
        // Add index to invoices.customer_email for faster searches
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('customer_email');
        });

        // Add index to quotes.customer_email for faster searches
        Schema::table('quotes', function (Blueprint $table) {
            $table->index('customer_email');
        });

        // Add index to customers.email for faster lookups
        Schema::table('customers', function (Blueprint $table) {
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['customer_email']);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex(['customer_email']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });
    }
};
