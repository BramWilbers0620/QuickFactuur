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
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->enum('status', ['concept', 'verzonden', 'betaald', 'te_laat'])->default('concept')->after('pdf_path');
            }
            if (!Schema::hasColumn('invoices', 'template')) {
                $table->string('template', 50)->default('modern')->after('status');
            }
            if (!Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('template');
            }
            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('sent_at');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('invoices', 'brand_color')) {
                $table->string('brand_color', 10)->default('#2563eb')->after('due_date');
            }
            if (!Schema::hasColumn('invoices', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('brand_color');
            }
            if (!Schema::hasColumn('invoices', 'vat_rate')) {
                $table->integer('vat_rate')->default(21)->after('logo_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = ['status', 'template', 'sent_at', 'paid_at', 'due_date', 'brand_color', 'logo_path', 'vat_rate'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
