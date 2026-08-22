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
            // 移除原有的外键约束（名称沿用原始列名 patient_id）
            $table->dropForeign('consumption_records_patient_id_foreign');

            // 确保 patient_profile_id 可空（已在部分执行中完成，此处为幂等保障）
            $table->unsignedBigInteger('patient_profile_id')->nullable()->comment('关联客户ID（散客时为空）')->change();

            // 确保 is_anonymous 字段存在
            if (! Schema::hasColumn('consumption_records', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->comment('是否为无档案散客')->after('patient_profile_id');
            }

            // 重新添加可空的外键约束
            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_records', function (Blueprint $table) {
            // 移除可空外键
            $table->dropForeign('consumption_records_patient_profile_id_foreign');

            // 恢复为非空
            $table->unsignedBigInteger('patient_profile_id')->nullable(false)->comment('关联客户ID')->change();

            // 重新添加原有外键（cascade on delete）
            $table->foreign('patient_profile_id', 'consumption_records_patient_id_foreign')->references('id')->on('patient_profiles')->onDelete('cascade');

            // 移除 is_anonymous 字段
            if (Schema::hasColumn('consumption_records', 'is_anonymous')) {
                $table->dropColumn('is_anonymous');
            }
        });
    }
};
