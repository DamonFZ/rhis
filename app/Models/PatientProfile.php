<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientProfile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'join_date' => 'date',
    ];

    public function consumptionRecords(): HasMany
    {
        return $this->hasMany(ConsumptionRecord::class, 'patient_profile_id');
    }

    public function patientPackages(): HasMany
    {
        return $this->hasMany(PatientPackage::class, 'patient_profile_id');
    }

    /**
     * 当前有效套餐：状态为 active、剩余次数 > 0、且未过期
     */
    public function activePackages(): HasMany
    {
        return $this->hasMany(PatientPackage::class, 'patient_profile_id')
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where(function (Builder $query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now()->toDateString());
            });
    }

    public function latestPackage(): HasOne
    {
        return $this->hasOne(PatientPackage::class, 'patient_profile_id')
            ->latestOfMany('purchase_date');
    }

    public function physicalAssessments(): HasMany
    {
        return $this->hasMany(PhysicalAssessment::class, 'patient_profile_id');
    }

    public function imagingRecords(): HasMany
    {
        return $this->hasMany(ImagingRecord::class, 'patient_profile_id');
    }

    public function latestImagingRecord(): HasOne
    {
        return $this->hasOne(ImagingRecord::class, 'patient_profile_id')
            ->latestOfMany('treatment_date');
    }

    public function latestConsumptionRecord(): HasOne
    {
        return $this->hasOne(ConsumptionRecord::class, 'patient_profile_id')
            ->latestOfMany('treatment_date');
    }
}
