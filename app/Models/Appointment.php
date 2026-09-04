<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_profile_id',
        'therapist_id',
        'start_time',
        'end_time',
        'remark',
        'status',
        'is_guest',
        'guest_name',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'integer',
        'is_guest' => 'boolean',
    ];

    /**
     * 预约客户展示名称（统一访问器：兼容会员与散客）
     */
    public function getClientNameAttribute(): string
    {
        return $this->is_guest
            ? trim(($this->guest_name ?? '').' (无档案)')
            : (optional($this->patientProfile)->name ?? '未知客户');
    }

    public function patientProfile()
    {
        return $this->belongsTo(PatientProfile::class);
    }

    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }
}
