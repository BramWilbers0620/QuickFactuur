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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('quote_number')->unique();

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

            // Quote details
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->text('description')->nullable();
            $table->json('items')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->integer('vat_rate')->default(21);
            $table->text('notes')->nullable();
            $table->string('pdf_path')->nullable();

            // Status and styling
            $table->enum('status', ['concept', 'verzonden', 'geaccepteerd', 'afgewezen', 'verlopen'])->default('concept');
            $table->string('template', 50)->default('modern');
            $table->string('brand_color', 10)->default('#2563eb');
            $table->string('logo_path')->nullable();

            // Tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('converted_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('quote_date');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
