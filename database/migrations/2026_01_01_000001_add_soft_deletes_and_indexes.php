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
        if (!Schema::hasColumn('invoices', 'deleted_at')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add indexes to invoices (silently ignore if already exists)
        $this->addIndexSafely('invoices', 'user_id');
        $this->addIndexSafely('invoices', 'status');
        $this->addIndexSafely('invoices', 'due_date');
        $this->addIndexSafely('invoices', ['status', 'due_date'], 'invoices_status_due_date_index');

        // Handle unique constraint change for invoices
        $this->dropUniqueSafely('invoices', 'invoice_number');
        $this->addUniqueSafely('invoices', ['user_id', 'invoice_number']);

        // Add soft deletes to quotes
        if (!Schema::hasColumn('quotes', 'deleted_at')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add indexes to quotes
        $this->addIndexSafely('quotes', 'user_id');
        $this->addIndexSafely('quotes', 'valid_until');
        $this->addIndexSafely('quotes', 'status');

        // Handle unique constraint change for quotes
        $this->dropUniqueSafely('quotes', 'quote_number');
        $this->addUniqueSafely('quotes', ['user_id', 'quote_number']);

        // Add soft deletes to customers
        if (!Schema::hasColumn('customers', 'deleted_at')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        $this->addIndexSafely('customers', 'user_id');
    }

    private function addIndexSafely(string $table, string|array $columns, ?string $name = null): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                if ($name) {
                    $t->index($columns, $name);
                } else {
                    $t->index($columns);
                }
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
    }

    private function addUniqueSafely(string $table, array $columns): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($columns) {
                $t->unique($columns);
            });
        } catch (\Exception $e) {
            // Unique constraint already exists, ignore
        }
    }

    private function dropUniqueSafely(string $table, string $column): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->dropUnique([$column]);
            });
        } catch (\Exception $e) {
            // Unique constraint doesn't exist, ignore
        }
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
