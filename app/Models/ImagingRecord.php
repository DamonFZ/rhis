<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagingRecord extends Model
{
    protected $fillable = [
        'patient_profile_id',
        'record_no',
        'record_type',
        'treatment_date',
        'photo_urls',
        'video_url',
        'remark',
    ];

    protected $casts = [
        'record_type' => 'integer',
        'treatment_date' => 'date',
        'photo_urls' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record) {
            if (empty($record->record_no)) {
                $record->record_no = 'IR' . date('YmdHis') . rand(100, 999);
            }
            if (empty($record->treatment_date)) {
                $record->treatment_date = now()->toDateString();
            }
        });
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }
}