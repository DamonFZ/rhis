<?php

namespace App\Models;

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
