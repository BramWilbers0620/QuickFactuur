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
        // Add soft deletes to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->softDeletes();

            // Add index on user_id for faster queries
            $table->index('user_id');

            // Add index on status for filtering
            $table->index('status');

            // Add index on due_date for overdue invoice queries
            $table->index('due_date');

            // Add composite index for overdue queries (status + due_date)
            $table->index(['status', 'due_date']);

            // Add composite unique constraint for invoice numbers per user
            // First drop the global unique constraint
            $table->dropUnique(['invoice_number']);
            $table->unique(['user_id', 'invoice_number']);
        });

        // Add soft deletes to quotes
        Schema::table('quotes', function (Blueprint $table) {
            $table->softDeletes();

            // Add index on user_id for faster queries
            $table->index('user_id');

            // Add index on valid_until for expired quote queries
            $table->index('valid_until');

            // Add index on status for filtering
            $table->index('status');

            // Add composite unique constraint for quote numbers per user
            $table->dropUnique(['quote_number']);
            $table->unique(['user_id', 'quote_number']);
        });

        // Add soft deletes to customers
        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();

            // Add index on user_id for faster queries
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['status', 'due_date']);
            $table->dropUnique(['user_id', 'invoice_number']);
            $table->unique('invoice_number');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['user_id']);
            $table->dropIndex(['valid_until']);
            $table->dropIndex(['status']);
            $table->dropUnique(['user_id', 'quote_number']);
            $table->unique('quote_number');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['user_id']);
        });
    }
};
