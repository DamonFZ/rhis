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
        Schema::table('consumption_records', function (Blueprint $table) {
            $table->string('source_type', 20)->default('member')->comment('开单类型：member-会员，trial-体验/散客')->after('is_anonymous');
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
    }
};
