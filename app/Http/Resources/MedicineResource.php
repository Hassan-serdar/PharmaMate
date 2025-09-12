<?php

namespace App\Http\Resources;

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user() && ($request->user()->role === Role::ADMIN || $request->user()->role === Role::SUPER_ADMIN);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'active_ingredient' => $this->active_ingredient,
            'dosage' => $this->dosage,
            'type' => $this->type,

            'image_url' => $this->when($this->image_path, Storage::url($this->image_path)),

            $this->mergeWhen($isAdmin, [
                'manufacturer' => $this->manufacturer,
                'description' => $this->description,
            ]),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
