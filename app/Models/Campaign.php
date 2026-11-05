<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function rehabPackages(): BelongsToMany
    {
        return $this->belongsToMany(RehabPackage::class)
            ->withPivot('extra_count')
            ->withTimestamps();
    }

    /**
     * 筛选正在进行中的活动：is_active = true 且 now() 在 [start_date, end_date] 之间
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query
            ->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now);
    }

    /**
     * 判断活动是否正在进行中
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();

        return $this->is_active
            && $this->start_date <= $now
            && $this->end_date >= $now;
    }

    /**
     * 查询指定套餐在当前进行中的活动中可获得的赠送次数
     * 返回 null 表示该套餐没有参与任何进行中的活动
     */
    public static function getActiveExtraCountForPackage(int $rehabPackageId): ?array
    {
        $campaign = static::active()
            ->whereHas('rehabPackages', function (Builder $query) use ($rehabPackageId) {
                $query->where('rehab_packages.id', $rehabPackageId);
            })
            ->with(['rehabPackages' => function ($query) use ($rehabPackageId) {
                $query->where('rehab_packages.id', $rehabPackageId);
            }])
            ->first();

        if (! $campaign) {
            return null;
        }

        $pivot = $campaign->rehabPackages->first()?->pivot;

        if (! $pivot || (int) $pivot->extra_count <= 0) {
            return null;
        }

        return [
            'campaign' => $campaign,
            'extra_count' => (int) $pivot->extra_count,
        ];
    }
}
