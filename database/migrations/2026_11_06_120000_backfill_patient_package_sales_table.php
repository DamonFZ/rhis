<?php

use App\Models\PatientPackage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 回填 patient_package_sales 中间表
     * 将历史 patient_packages.salesperson_id + sales_commission 数据
     * 同步到 patient_package_sales，确保提成报表能正确读取。
     * 幂等：已存在的 (patient_package_id, user_id) 不会重复插入。
     */
    public function up(): void
    {
        $packages = PatientPackage::whereNotNull('salesperson_id')->get();

        $inserts = [];
        $now = now();

        foreach ($packages as $package) {
            // 跳过已存在中间表记录的套餐，保证幂等
            $exists = DB::table('patient_package_sales')
                ->where('patient_package_id', $package->id)
                ->where('user_id', $package->salesperson_id)
                ->exists();

            if ($exists) {
                continue;
            }

            $inserts[] = [
                'patient_package_id' => $package->id,
                'user_id' => $package->salesperson_id,
                'commission_amount' => $package->sales_commission ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($inserts)) {
            DB::table('patient_package_sales')->insert($inserts);
        }
    }

    public function down(): void
    {
        // 不做删除操作，避免误删后续通过业务逻辑产生的分成记录
    }
};
