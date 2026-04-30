<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_profile_id',
        'package_code',
        'package_name',
        'package_type',
        'total_sessions',
        'remaining_sessions',
        'price',
        'original_price',
        'average_price',
        'status',
        'description',
        'is_extendable',
        'extension_days',
        'is_shareable',
        'purchase_date',
        'expiry_date',
        'salesperson_id',
        'sales_type',
        'sales_commission',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'average_price' => 'decimal:2',
        'is_extendable' => 'boolean',
        'is_shareable' => 'boolean',
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'sales_commission' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $package) {
            if (empty($package->purchase_date)) {
                $package->purchase_date = now()->toDateString();
            }
        });

        static::saving(function ($model) {
            // 提成比例字典：1-自主开发(3%), 2-康复续卡(1%), 3-协助开单(2%)
            $commissionRates = [
                1 => 0.03,
                2 => 0.01,
                3 => 0.02,
            ];

            // 如果选择了开单类型，并且有价格，则自动计算提成金额
            if ($model->sales_type && isset($commissionRates[$model->sales_type]) && $model->price > 0) {
                $model->sales_commission = $model->price * $commissionRates[$model->sales_type];
            } else {
                $model->sales_commission = 0;
            }
        });
    }

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function salesperson(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'salesperson_id');
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
