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
            // Company profile fields
            $table->string('company_name')->nullable()->after('email');
            $table->string('company_address')->nullable()->after('company_name');
            $table->string('company_phone', 50)->nullable()->after('company_address');
            $table->string('company_kvk', 20)->nullable()->after('company_phone');
            $table->string('company_iban', 50)->nullable()->after('company_kvk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_address',
                'company_phone',
                'company_kvk',
                'company_iban',
            ]);
        });
    }
};
