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
            'owner' => $this->user->name ?? null,
            'phone_number' => $this->phone_number,
            'address_line_1' => $this->address_line_1,
            'city' => $this->city,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'status' => $this->status,
            'medicines' => $this->whenLoaded('medicines', function () {
                return $this->medicines->map(function ($medicine) {
                    return [
                        'id' => $medicine->id,
                        'name' => $medicine->name,
                        'type' => $medicine->type->value,
                        'quantity' => $medicine->pivot->quantity,
                        'price' => $medicine->pivot->price,
                    ];
                });
            }),
        ];    
    }
}
