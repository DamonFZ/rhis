<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charge_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique()->comment('项目编码');
            $table->string('name', 200)->comment('项目名称');
            $table->decimal('price', 10, 2)->default(0)->comment('单价');
            $table->string('unit', 20)->default('次')->comment('单位');
            $table->string('category', 100)->nullable()->comment('分类');
            $table->text('description')->nullable()->comment('描述');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-禁用，1-启用');
            $table->timestamps();

            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charge_items');
    }
};