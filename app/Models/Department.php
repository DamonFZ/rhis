<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'level',
        'sort',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($department) {
            if (is_null($department->parent_id) || $department->parent_id === '') {
                $department->parent_id = 0;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('is_primary');
    }
}