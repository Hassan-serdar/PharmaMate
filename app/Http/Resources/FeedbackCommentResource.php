<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'is_private_note' => $this->is_private,
            'commented_at' => $this->created_at->diffForHumans(),
            'author' => [
                'name' => $this->user->firstname,
                'role' => $this->user->role->value,
            ]
        ];
    }
}