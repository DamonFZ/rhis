<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_profile_id')->comment('关联客户ID');
            $table->string('assessment_no', 50)->unique()->comment('评估编号');
            $table->date('assessment_date')->comment('评估日期');
            $table->tinyInteger('assessment_type')->default(1)->comment('类型：1-初评，2-复评，3-末评');
            $table->decimal('height', 5, 2)->nullable()->comment('身高(cm)');
            $table->decimal('weight', 5, 2)->nullable()->comment('体重(kg)');
            $table->decimal('bmi', 5, 2)->nullable()->comment('BMI');
            $table->decimal('body_fat_rate', 5, 2)->nullable()->comment('体脂率(%)');
            $table->json('circumference')->nullable()->comment('围度数据');
            $table->json('flexibility')->nullable()->comment('柔软度数据');
            $table->json('posture_tags')->nullable()->comment('体态标签');
            $table->string('body_canvas_path', 255)->nullable()->comment('图谱路径');
            $table->text('remark')->nullable()->comment('备注');
            $table->tinyInteger('status')->default(0)->comment('状态：0-草稿，1-已完成');
            $table->timestamps();

            $table->foreign('patient_profile_id')
                ->references('id')
                ->on('patient_profiles')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_assessments');
    }
};