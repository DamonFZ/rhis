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
        Schema::create('patient_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_profile_id')->comment('关联客户ID');
            $table->string('package_name', 200)->comment('套餐名称');
            $table->integer('total_sessions')->default(0)->comment('总次数');
            $table->integer('remaining_sessions')->default(0)->comment('剩余次数');
            $table->decimal('price', 10, 2)->default(0)->comment('套餐价格');
            $table->string('status', 20)->default('active')->comment('状态：active-有效, completed-已完成');
            $table->timestamps();

            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->index('status');
            $table->index('patient_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_packages');
    }
};
