<?php

namespace App\Models\Libraries;

use App\Models\User\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibTransaction extends Model
{
    use HasFactory;

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
}
