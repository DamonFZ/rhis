<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = ['service_commission', 'sales_type_1_rate', 'sales_type_2_rate', 'sales_type_3_rate'];
}
