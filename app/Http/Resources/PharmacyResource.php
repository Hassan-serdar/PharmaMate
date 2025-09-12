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
            'name' => $this->name,
            'owner_name' => $this->user->firstname . ' ' . $this->user->lastname,
            'phone_number' => $this->phone_number,
            'address_line_1' => $this->address_line_1,
            'city' => $this->city,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'status' => $this->status,
            'medicines' => MedicineResource::collection($this->whenLoaded('medicines')),
        ];    
    }
}
