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
        'service_commission',
        'valid_start_date',
        'valid_end_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'average_price' => 'decimal:2',
        'status' => 'boolean',
        'is_extendable' => 'boolean',
        'is_shareable' => 'boolean',
        'service_commission' => 'decimal:2',
        'valid_start_date' => 'date',
        'valid_end_date' => 'date',
    ];
}
