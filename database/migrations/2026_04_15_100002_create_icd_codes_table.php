<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('icd_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('ICD编码');
            $table->string('name', 200)->comment('疾病名称');
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
        Schema::dropIfExists('icd_codes');
    }
};