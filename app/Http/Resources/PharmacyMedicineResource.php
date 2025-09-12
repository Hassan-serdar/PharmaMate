<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PharmacyMedicineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'medicine' => new MedicineResource($this),            
            'inventory' => [
                'price' => $this->whenPivotLoaded('medicine_pharmacy', fn() => $this->pivot->price),
                'quantity' => $this->whenPivotLoaded('medicine_pharmacy', fn() => $this->pivot->quantity),
                'last_updated' => $this->whenPivotLoaded('medicine_pharmacy', fn() => $this->pivot->updated_at->diffForHumans()),
            ]
        ];
    }
}
