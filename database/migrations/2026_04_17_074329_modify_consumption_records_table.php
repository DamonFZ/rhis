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
        Schema::table('consumption_records', function (Blueprint $table) {
            // 删除 package_id 外键和字段
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
            
            // 添加 package_name 字段到 patient_id 之后
            $table->string('package_name', 200)->nullable()->comment('套餐名称')->after('patient_id');
            
            // 删除 customer_signature_path 字段
            $table->dropColumn('customer_signature_path');
            
            // 重命名 patient_id 为 patient_profile_id
            $table->renameColumn('patient_id', 'patient_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_records', function (Blueprint $table) {
            // 重命名 patient_profile_id 回 patient_id
            $table->renameColumn('patient_profile_id', 'patient_id');
            
            // 恢复 package_id 字段和外键
            $table->unsignedBigInteger('package_id')->nullable()->comment('关联套餐ID')->after('patient_id');
            $table->foreign('package_id')->references('id')->on('rehab_packages')->nullOnDelete();
            
            // 删除 package_name 字段
            $table->dropColumn('package_name');
            
            // 恢复 customer_signature_path 字段
            $table->string('customer_signature_path')->nullable()->comment('签名图片路径')->after('treatment_content');
        });
    }
};
