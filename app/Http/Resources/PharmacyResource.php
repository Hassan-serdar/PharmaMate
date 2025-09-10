<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PharmacyResource extends JsonResource
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
            'pharmacy_name' => $this->name,
            'contact_number' => $this->phone_number,
            'address' => [
                'full_address' => $this->address_line_1,
                'city' => $this->city,
            ],
            'working_hours' => [
                'opens_at' => $this->opening_time,
                'closes_at' => $this->closing_time,
            ],
            'current_status' => $this->status->value,
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'owner' => [
                'name' => $this->user->firstname . ' ' . $this->user->lastname,
            ],
        ];
    }
}
