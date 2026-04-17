<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IcdCode extends Model
{
    protected $table = 'icd_codes';

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}