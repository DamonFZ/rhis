<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('service_commission', 10, 2)->default(15.00)->comment('单次服务提成(元)');
            $table->decimal('sales_type_1_rate', 5, 2)->default(3.00)->comment('自主开发提成比例(%)');
            $table->decimal('sales_type_2_rate', 5, 2)->default(1.00)->comment('康复续卡提成比例(%)');
            $table->decimal('sales_type_3_rate', 5, 2)->default(2.00)->comment('协助开单提成比例(%)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};
