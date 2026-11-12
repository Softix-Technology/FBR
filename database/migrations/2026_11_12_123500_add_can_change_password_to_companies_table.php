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
        if (!Schema::hasColumn('companies', 'can_change_password')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->boolean('can_change_password')->default(false)->after('logo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('companies', 'can_change_password')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('can_change_password');
            });
        }
    }
};

