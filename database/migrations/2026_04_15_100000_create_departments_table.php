<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父科室ID，顶级科室为0');
            $table->string('name', 100)->comment('科室名称');
            $table->string('code', 50)->nullable()->comment('科室编码');
            $table->unsignedTinyInteger('level')->default(1)->comment('科室层级');
            $table->integer('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-禁用，1-启用');
            $table->text('description')->nullable()->comment('科室描述');
            $table->timestamps();

            $table->index('parent_id');
            $table->index('level');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};