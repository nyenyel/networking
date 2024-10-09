<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\User\InvitedUser;
use App\Models\User\StoreInfo;
use App\Models\User\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'admin',
        'email',
        'email_verified_at',
        'password',
        'photo_id',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function storeInfo()
    {
        return $this->hasOne(StoreInfo::class, 'user_id');
    }

    public function store_referrer() {
        return $this->hasMany(StoreInfo::class, 'user_id');
    }

    public function store_invitee() {
        return $this->hasMany(StoreInfo::class, 'invited_by');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function user_referrer() {
        return $this->hasMany(InvitedUser::class, 'user_id');
    }

    public function user_invitee()
{
    return $this->hasMany(InvitedUser::class, 'user_id')
                ->with('invited.user_invitee'); // Eager load the invited users
}



}
