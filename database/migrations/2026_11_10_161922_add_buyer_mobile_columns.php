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
        if (Schema::hasTable('buyers') && !Schema::hasColumn('buyers', 'mobile_number')) {
            Schema::table('buyers', function (Blueprint $table) {
                $table->string('mobile_number', 50)->nullable()->after('address');
            });
        }

        if (Schema::hasTable('sale_invoice_fbr') && !Schema::hasColumn('sale_invoice_fbr', 'buyer_mobile')) {
            Schema::table('sale_invoice_fbr', function (Blueprint $table) {
                $table->string('buyer_mobile', 50)->nullable()->after('buyer_address');
            });
        }

        if (Schema::hasTable('draft_invoices') && !Schema::hasColumn('draft_invoices', 'buyer_mobile')) {
            Schema::table('draft_invoices', function (Blueprint $table) {
                $table->string('buyer_mobile', 50)->nullable()->after('buyer_address');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('buyers') && Schema::hasColumn('buyers', 'mobile_number')) {
            Schema::table('buyers', function (Blueprint $table) {
                $table->dropColumn('mobile_number');
            });
        }

        if (Schema::hasTable('sale_invoice_fbr') && Schema::hasColumn('sale_invoice_fbr', 'buyer_mobile')) {
            Schema::table('sale_invoice_fbr', function (Blueprint $table) {
                $table->dropColumn('buyer_mobile');
            });
        }

        if (Schema::hasTable('draft_invoices') && Schema::hasColumn('draft_invoices', 'buyer_mobile')) {
            Schema::table('draft_invoices', function (Blueprint $table) {
                $table->dropColumn('buyer_mobile');
            });
        }
    }
};
