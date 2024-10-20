<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'before_user_id',
        'current_user_id',
    ];

    public function userBefore(): BelongsTo {
        return $this->belongsTo(User::class, 'before_user_id');
    }

    public function userCurrent():BelongsTo {
        return $this->belongsTo(User::class, 'current_user_id');
    }
}
