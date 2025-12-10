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
        Schema::table('invoices', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('invoices', 'company_email')) {
                $table->string('company_email')->nullable()->after('company_address');
            }
            if (!Schema::hasColumn('invoices', 'company_phone')) {
                $table->string('company_phone', 50)->nullable()->after('company_email');
            }
            if (!Schema::hasColumn('invoices', 'company_kvk')) {
                $table->string('company_kvk', 20)->nullable()->after('company_phone');
            }
            if (!Schema::hasColumn('invoices', 'company_iban')) {
                $table->string('company_iban', 50)->nullable()->after('company_kvk');
            }
            if (!Schema::hasColumn('invoices', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('invoices', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('invoices', 'customer_phone')) {
                $table->string('customer_phone', 50)->nullable()->after('customer_address');
            }
            if (!Schema::hasColumn('invoices', 'customer_vat')) {
                $table->string('customer_vat', 50)->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('invoices', 'payment_terms')) {
                $table->string('payment_terms', 20)->default('14')->after('invoice_date');
            }
            if (!Schema::hasColumn('invoices', 'items')) {
                $table->json('items')->nullable()->after('description');
            }
            if (!Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('total');
            }
        });

        // Make company_vat nullable if it exists
        if (Schema::hasColumn('invoices', 'company_vat')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('company_vat', 50)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't remove columns in down() to prevent data loss
    }
};
