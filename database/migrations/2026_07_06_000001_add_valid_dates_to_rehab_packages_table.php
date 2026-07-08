<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->date('valid_start_date')->nullable()->after('validity_days');
            $table->date('valid_end_date')->nullable()->after('valid_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('rehab_packages', function (Blueprint $table) {
            $table->dropColumn(['valid_start_date', 'valid_end_date']);
        });
    }
};
