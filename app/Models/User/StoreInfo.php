<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInfo extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'invited_by',
        'invitation_code',
        'points',
        'unpaid',
        'status',
        'invited_users_count',
        'daily_points_timestamp',
        'today_points',
        'is_reached',
    ];

    protected $casts = [
        'daily_points_timestamp' => 'datetime',
        'last_redeemed' => 'datetime',
        'is_reached' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
