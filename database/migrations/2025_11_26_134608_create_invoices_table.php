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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();

            // Company details
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_email')->nullable();
            $table->string('company_phone', 50)->nullable();
            $table->string('company_vat', 50)->nullable();
            $table->string('company_kvk', 20)->nullable();
            $table->string('company_iban', 50)->nullable();

            // Customer details
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('customer_vat', 50)->nullable();

            // Invoice details
            $table->date('invoice_date');
            $table->string('payment_terms', 20)->default('14');
            $table->text('description');
            $table->json('items')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('invoice_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
