<?php

namespace App\Http\Resources;

use App\Http\Resources\Resources\InvitedUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'store_no' => $this->store_no,
            'email' => $this->email,
            'invited_users' => InvitedUserResource::collection($this->whenLoaded('user_invitee')),
        ];
    }
}