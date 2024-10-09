<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitedUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'invited_user',
    ];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invited()
    {
        return $this->belongsTo(User::class, 'invited_user');
    }

}
