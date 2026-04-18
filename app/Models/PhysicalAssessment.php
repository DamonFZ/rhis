<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalAssessment extends Model
{
    protected $fillable = [
        'patient_profile_id',
        'assessment_no',
        'assessment_date',
        'assessment_type',
        'height',
        'weight',
        'bmi',
        'body_fat_rate',
        'circumference',
        'flexibility',
        'posture_tags',
        'body_canvas_path',
        'remark',
        'status',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'assessment_type' => 'integer',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'bmi' => 'decimal:2',
        'body_fat_rate' => 'decimal:2',
        'circumference' => 'array',
        'flexibility' => 'array',
        'posture_tags' => 'array',
        'status' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $assessment) {
            if (empty($assessment->assessment_no)) {
                $assessment->assessment_no = 'PA' . date('YmdHis') . rand(100, 999);
            }
            if (empty($assessment->assessment_date)) {
                $assessment->assessment_date = now()->toDateString();
            }
            if (empty($assessment->status)) {
                $assessment->status = 0;
            }
        });
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }
}