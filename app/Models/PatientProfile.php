<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}