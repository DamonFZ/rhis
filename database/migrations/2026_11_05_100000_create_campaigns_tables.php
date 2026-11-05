<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('活动名称');
            $table->dateTime('start_date')->comment('活动开始时间');
            $table->dateTime('end_date')->comment('活动结束时间');
            $table->boolean('is_active')->default(true)->comment('强制启停开关');
            $table->timestamps();
        });

        Schema::create('campaign_rehab_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rehab_package_id')->constrained()->cascadeOnDelete();
            $table->integer('extra_count')->default(0)->comment('赠送次数');
            $table->timestamps();

            $table->unique(['campaign_id', 'rehab_package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_rehab_package');
        Schema::dropIfExists('campaigns');
    }
};
