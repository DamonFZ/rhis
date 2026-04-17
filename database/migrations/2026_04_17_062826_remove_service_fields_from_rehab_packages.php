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
            $table->dropColumn(['service_composition', 'service_items']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->text('service_composition')->nullable()->comment('服务内容组合')->after('is_shareable');
            $table->json('service_items')->nullable()->comment('服务项目JSON')->after('service_composition');
        });
    }
};
