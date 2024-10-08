<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'used_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
