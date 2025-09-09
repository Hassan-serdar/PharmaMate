<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->firstname . ' ' . $this->lastname,
            'email' => $this->email,
            'phone' => $this->phonenumber,
            'role' => $this->role->value,
            'member_since' => $this->created_at->toFormattedDateString(),
        ];
    }
}
