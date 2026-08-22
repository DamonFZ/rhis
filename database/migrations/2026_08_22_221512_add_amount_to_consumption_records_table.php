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
            $table->decimal('amount', 10, 2)->nullable()->comment('服务金额（散客开单时使用）')->after('is_anonymous');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_records', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
