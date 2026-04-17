<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            'single' => '单次',
            'course' => '疗程卡',
            'monthly' => '月卡',
            'quarterly' => '季卡',
            'special' => '特惠次卡',
            'item' => '单项服务',
        ];

        foreach ($mapping as $english => $chinese) {
            DB::table('rehab_packages')
                ->where('package_type', $english)
                ->update(['package_type' => $chinese]);

            DB::table('patient_packages')
                ->where('package_type', $english)
                ->update(['package_type' => $chinese]);
        }
    }

    public function down(): void
    {
        $mapping = [
            '单次' => 'single',
            '疗程卡' => 'course',
            '月卡' => 'monthly',
            '季卡' => 'quarterly',
            '特惠次卡' => 'special',
            '单项服务' => 'item',
        ];

        foreach ($mapping as $chinese => $english) {
            DB::table('rehab_packages')
                ->where('package_type', $chinese)
                ->update(['package_type' => $english]);

            DB::table('patient_packages')
                ->where('package_type', $chinese)
                ->update(['package_type' => $english]);
        }
    }
};
