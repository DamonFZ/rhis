<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imaging_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_profile_id')->comment('关联客户ID');
            $table->string('record_no', 50)->unique()->comment('记录编号');
            $table->tinyInteger('record_type')->default(1)->comment('类型：1-治疗前，2-治疗后');
            $table->date('treatment_date')->comment('治疗日期');
            $table->json('photo_urls')->nullable()->comment('图片路径');
            $table->string('video_url', 255)->nullable()->comment('视频路径');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->foreign('patient_profile_id')
                ->references('id')
                ->on('patient_profiles')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_records');
    }
};