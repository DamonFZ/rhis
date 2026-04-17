<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Models\PatientPackage;

class ConsumptionRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'treatment_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            // 自动扣减关联套餐包的剩余次数
            if ($record->patient_package_id && $record->deducted_sessions > 0) {
                $package = PatientPackage::find($record->patient_package_id);
                if ($package && $package->isActive()) {
                    // 使用数据库事务确保数据一致性
                    DB::transaction(function () use ($package, $record) {
                        $newRemaining = $package->remaining_sessions - $record->deducted_sessions;
                        if ($newRemaining < 0) {
                            throw new \Exception('剩余次数不足，无法扣减');
                        }
                        
                        $package->remaining_sessions = $newRemaining;
                        
                        // 如果剩余次数为0，标记为已完成
                        if ($newRemaining <= 0) {
                            $package->markAsCompleted();
                        } else {
                            $package->save();
                        }
                        
                        // 更新记录的剩余次数字段
                        $record->remaining_sessions = $newRemaining;
                    });
                }
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function patientPackage(): BelongsTo
    {
        return $this->belongsTo(PatientPackage::class, 'patient_package_id');
    }
}