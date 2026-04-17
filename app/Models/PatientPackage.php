<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_profile_id',
        'package_name',
        'total_sessions',
        'remaining_sessions',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function consumptionRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ConsumptionRecord::class, 'patient_package_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
