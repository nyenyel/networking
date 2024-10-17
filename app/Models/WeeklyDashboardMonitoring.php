<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyDashboardMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_sold',
        'product_purchased',
        'company_revenue'
    ];
}
