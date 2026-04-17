<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->unique()->comment('客户编号');
            $table->string('name')->comment('姓名');
            $table->string('phone')->nullable()->comment('联系电话');
            $table->date('join_date')->nullable()->comment('建档日期');
            $table->string('membership_no')->nullable()->comment('会员号');
            $table->text('initial_symptoms')->nullable()->comment('初始症状');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};