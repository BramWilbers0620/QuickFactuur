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
        // Skip if columns already exist (created in initial migration)
        if (Schema::hasColumn('invoices', 'company_email')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            // Company extra fields
            $table->string('company_email')->nullable()->after('company_address');
            $table->string('company_phone')->nullable()->after('company_email');
            $table->string('company_kvk', 20)->nullable()->after('company_vat');
            $table->string('company_iban', 50)->nullable()->after('company_kvk');

            // Customer extra fields
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->string('customer_address')->nullable()->after('customer_email');
            $table->string('customer_phone')->nullable()->after('customer_address');
            $table->string('customer_vat', 50)->nullable()->after('customer_phone');

            // Invoice extra fields
            $table->string('payment_terms', 20)->default('30')->after('invoice_date');
            $table->text('notes')->nullable()->after('total');

            // Items stored as JSON for multiple line items
            $table->json('items')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'company_email',
                'company_phone',
                'company_kvk',
                'company_iban',
                'customer_email',
                'customer_address',
                'customer_phone',
                'customer_vat',
                'payment_terms',
                'notes',
                'items',
            ]);
        });
    }
};
