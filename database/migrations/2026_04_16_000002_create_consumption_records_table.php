<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->comment('关联客户ID');
            $table->unsignedBigInteger('package_id')->nullable()->comment('关联套餐ID');
            $table->integer('deducted_sessions')->default(1)->comment('本次扣减次数');
            $table->integer('remaining_sessions')->default(0)->comment('剩余次数');
            $table->date('treatment_date')->comment('治疗日期');
            $table->text('treatment_content')->nullable()->comment('治疗内容');
            $table->string('customer_signature_path')->nullable()->comment('签名图片路径');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('rehab_packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_records');
    }
};