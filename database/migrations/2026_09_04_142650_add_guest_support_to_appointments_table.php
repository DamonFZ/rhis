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
        Schema::table('appointments', function (Blueprint $table) {
            // 1. 先删除原有的外键约束，再允许 patient_profile_id 为空
            $table->dropForeign(['patient_profile_id']);
            $table->foreignId('patient_profile_id')->nullable()->change();
            $table->foreign('patient_profile_id')
                ->references('id')
                ->on('patient_profiles')
                ->nullOnDelete();

            // 2. 散客支持字段
            $table->boolean('is_guest')->default(false)->comment('是否为散客预约')->after('patient_profile_id');
            $table->string('guest_name')->nullable()->comment('散客姓名')->after('is_guest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_profile_id']);
            $table->dropColumn('is_guest');
            $table->dropColumn('guest_name');

            // 恢复原外键约束（回滚时可能由于残留空值失败，仅供结构参考）
            $table->foreignId('patient_profile_id')->nullable(false)->change();
            $table->foreign('patient_profile_id')
                ->references('id')
                ->on('patient_profiles')
                ->cascadeOnDelete();
        });
    }
};
