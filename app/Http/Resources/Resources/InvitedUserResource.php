<?php

namespace App\Http\Resources\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitedUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->invited_user,
            'user' => new UserResource($this->whenLoaded('invited')),
            // Eager load the invited users
            'invited_users' => InvitedUserResource::collection($this->whenLoaded('invited.user_invitee')),
        ];
    }
}
