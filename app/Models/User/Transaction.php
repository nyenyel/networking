<?php

namespace App\Models\User;

use App\Models\Libraries\LibTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function lib_transactions() {
        return $this->belongsTo(LibTransaction::class);
    }
}
