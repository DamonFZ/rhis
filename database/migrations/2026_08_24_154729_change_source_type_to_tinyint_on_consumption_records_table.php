<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 先把现有 string 值映射为数字
        DB::table('consumption_records')
            ->where('source_type', 'member')
            ->update(['source_type' => '0']);

        DB::table('consumption_records')
            ->where('source_type', 'trial')
            ->update(['source_type' => '1']);

        // 修改列为 tinyint
        Schema::table('consumption_records', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });

        Schema::table('consumption_records', function (Blueprint $table) {
            $table->tinyInteger('source_type')->default(0)->comment('开单类型：0-会员，1-体验')->after('is_anonymous');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_records', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });

        Schema::table('consumption_records', function (Blueprint $table) {
            $table->string('source_type', 20)->default('member')->comment('开单类型：member-会员，trial-体验/散客')->after('is_anonymous');
        });

        // 回写 string 值
        DB::table('consumption_records')
            ->where('source_type', '0')
            ->update(['source_type' => 'member']);

        DB::table('consumption_records')
            ->where('source_type', '1')
            ->update(['source_type' => 'trial']);
    }
};
