<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. 修改 rehab_packages：重命名并设置服务提成默认值
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->renameColumn('commission_per_service', 'service_commission');
        });
        // 注意：SQLite 或某些低版本 MySQL rename 和 change 最好分开写
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->decimal('service_commission', 10, 2)->default(15.00)->comment('单次服务提成金额')->change();
        });

        // 2. 修改 patient_packages：增加销售提成相关字段
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->foreignId('salesperson_id')->nullable()->comment('销售归属员工ID')->constrained('users')->nullOnDelete();
            $table->tinyInteger('sales_type')->nullable()->comment('开单类型：1-自主开发(3%), 2-康复续卡(1%), 3-协助开单(2%)');
            $table->decimal('sales_commission', 10, 2)->default(0)->comment('本次销售提成金额');
        });
    }

    public function down(): void
    {
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->renameColumn('service_commission', 'commission_per_service');
        });
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->dropForeign(['salesperson_id']);
            $table->dropColumn(['salesperson_id', 'sales_type', 'sales_commission']);
        });
    }
};
