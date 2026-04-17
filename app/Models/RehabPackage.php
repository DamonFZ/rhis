<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RehabPackage extends Model
{
    protected $table = 'rehab_packages';

    protected $fillable = [
        'package_code',
        'name',
        'description',
        'price',
        'total_sessions',
        'validity_days',
        'status',
        'package_type',
        'original_price',
        'average_price',
        'is_extendable',
        'extension_days',
        'is_shareable',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'average_price' => 'decimal:2',
        'status' => 'boolean',
        'is_extendable' => 'boolean',
        'is_shareable' => 'boolean',
    ];


}