<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineSuggestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'suggested_medicine' => [
                'name' => $this->name,
                'active_ingredient' => $this->active_ingredient,
                'dosage' => $this->dosage,
                'type' => $this->type,
            ],
            'status' => $this->status,
            'rejection_reason' => $this->when($this->rejection_reason, $this->rejection_reason),
            'suggested_by' => new UserResource($this->whenLoaded('pharmacist')),
            'suggested_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
