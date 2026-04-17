<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('department_id')->comment('科室ID');
            $table->unsignedTinyInteger('is_primary')->default(0)->comment('是否主科室：0-否，1-是');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'department_id']);
            $table->index('department_id');
            $table->index('is_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_user');
    }
};