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
//            $table->string('package_type', 50)->default('single')->comment('套餐类型：single-单次, course-疗程卡, monthly-月卡, quarterly-季卡, special-特惠次卡, item-单项服务')->after('status');
            $table->decimal('original_price', 10, 2)->default(0)->comment('原始价格')->after('package_type');
            $table->decimal('average_price', 10, 2)->default(0)->comment('均价')->after('original_price');
            $table->boolean('is_extendable')->default(false)->comment('是否可延期')->after('average_price');
            $table->integer('extension_days')->default(0)->comment('可延期天数')->after('is_extendable');
            $table->boolean('is_shareable')->default(false)->comment('是否可共享')->after('extension_days');
            $table->text('service_composition')->nullable()->comment('服务内容组合')->after('is_shareable');
            $table->json('service_items')->nullable()->comment('服务项目JSON')->after('service_composition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->dropColumn([
                'package_type',
                'original_price',
                'average_price',
                'is_extendable',
                'extension_days',
                'is_shareable',
                'service_composition',
                'service_items',
            ]);
        });
    }
};
