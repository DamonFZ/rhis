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
            $table->unsignedBigInteger('patient_package_id')->nullable()->comment('关联客户套餐包ID')->after('patient_profile_id');
            $table->foreign('patient_package_id')->references('id')->on('patient_packages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_records', function (Blueprint $table) {
            $table->dropForeign(['patient_package_id']);
            $table->dropColumn('patient_package_id');
        });
    }
};
