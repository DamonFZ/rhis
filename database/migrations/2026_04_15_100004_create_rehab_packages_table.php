<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rehab_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_code', 50)->unique()->comment('套餐编码');
            $table->string('name', 200)->comment('套餐名称');
            $table->text('description')->nullable()->comment('套餐描述');
            $table->decimal('price', 10, 2)->default(0)->comment('套餐价格');
            $table->integer('total_sessions')->default(0)->comment('总次数');
            $table->integer('validity_days')->default(0)->comment('有效期（天）');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-禁用，1-启用');
            $table->string('package_type', 50)->default('single')->comment('套餐类型：single-单次, course-疗程卡, monthly-月卡, quarterly-季卡, special-特惠次卡, item-单项服务');
            $table->decimal('original_price', 10, 2)->default(0)->comment('原始价格');
            $table->decimal('average_price', 10, 2)->default(0)->comment('均价');
            $table->boolean('is_extendable')->default(false)->comment('是否可延期');
            $table->integer('extension_days')->default(0)->comment('可延期天数');
            $table->boolean('is_shareable')->default(false)->comment('是否可共享');
            $table->text('service_composition')->nullable()->comment('服务内容组合');
            $table->json('service_items')->nullable()->comment('服务项目JSON');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rehab_packages');
    }
};