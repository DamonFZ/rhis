<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->string('package_code', 50)->nullable()->comment('套餐编码')->after('patient_profile_id');
            $table->string('package_type', 50)->nullable()->comment('套餐类型')->after('package_code');
            $table->text('description')->nullable()->comment('套餐描述')->after('status');
            $table->boolean('is_extendable')->default(false)->comment('是否可延期')->after('description');
            $table->integer('extension_days')->default(0)->comment('可延期天数')->after('is_extendable');
            $table->boolean('is_shareable')->default(false)->comment('是否可共享')->after('extension_days');
            $table->date('purchase_date')->nullable()->comment('购买日期')->after('is_shareable');
            $table->date('expiry_date')->nullable()->comment('到期日期')->after('purchase_date');
            $table->decimal('original_price', 10, 2)->default(0)->comment('原价')->after('price');
            $table->decimal('average_price', 10, 2)->default(0)->comment('均价')->after('original_price');
        });
    }

    public function down(): void
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->dropColumn([
                'package_code',
                'package_type',
                'description',
                'is_extendable',
                'extension_days',
                'is_shareable',
                'purchase_date',
                'expiry_date',
                'original_price',
                'average_price',
            ]);
        });
    }
};
