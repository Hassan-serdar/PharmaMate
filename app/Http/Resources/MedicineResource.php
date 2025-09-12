<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active_ingredient' => $this->active_ingredient,
            'dosage' => $this->dosage,
            'type' => $this->type->value,
             'image_url' => $this->image_path ? Storage::url($this->image_path) : null,
            'description' => $this->description,
            'manufacturer' => $this->manufacturer,
        ];
    }
}
