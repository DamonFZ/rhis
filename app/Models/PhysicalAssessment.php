<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalAssessment extends Model
{
    protected $table = 'physical_assessments';

    protected $fillable = [
        'patient_id',
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
        'body_canvas_data',
        'assessor_id',
        'remark',
        'status',
        'created_by',
        'updated_by',
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
        'status' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}