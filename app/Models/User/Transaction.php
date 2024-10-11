<?php

namespace App\Models\User;

use App\Models\Libraries\LibTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_type',
        'status',
        'amount',
        'code'
    ];

    public function users(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function lib_transactions() {
        return $this->belongsTo(LibTransaction::class);
    }
}
