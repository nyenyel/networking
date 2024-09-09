<?php

namespace App\Models\Libraries;

use App\Models\User\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibTransaction extends Model
{
    use HasFactory;

    public function transactions() {
        return $this->belongsTo(Transaction::class);
    }
}
