<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user'            => new UserResource($this->whenLoaded('user')),
            'type'            => $this->type?->value,
            'subject'         => $this->subject,
            'message'         => $this->message,
            'status'          => $this->status?->value,
            'priority'        => $this->priority?->value,
            'assigned_to'     => new UserResource($this->whenLoaded('assignedTo')),
            'attachments'     => $this->attachments ?? [],
            'comments'        => FeedbackCommentResource::collection($this->whenLoaded('comments')),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
    ];
    }
}