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
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->decimal('commission_per_service', 10, 2)->default(0)->comment('单次服务提成金额')->after('is_shareable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->dropColumn('commission_per_service');
        });
    }
};
