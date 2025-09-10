<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\User;
use App\Enums\Role; // <-- 1. منستدعي الـ Enum تبع الأدوار
use App\Notifications\Admin\NewFeedbackReceivedNotification;
use App\Notifications\FeedbackUpdatedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification; // <-- 2. منستدعي الـ Facade تبع الإشعارات

class FeedbackService
{
    public function store(User $user, array $data, array $files = []): Feedback
    {
        $feedback = $user->feedback()->create($data);

        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $path = $file->store('feedback-attachments', 'public');
            $feedback->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
        
        $admins = User::whereIn('role', [Role::ADMIN, Role::SUPER_ADMIN])->get();
    
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewFeedbackReceivedNotification($feedback));
        }

        return $feedback;
    }

    // ... باقي الميثودات ما تغيرت ...
    public function updateStatus(Feedback $feedback, array $data): Feedback
    {
        $feedback->update($data);
        $feedback->user->notify(new FeedbackUpdatedNotification($feedback));
        return $feedback;
    }

    public function addComment(Feedback $feedback, User $admin, array $data): Feedback
    {
        $comment = $feedback->comments()->create([
            'user_id' => $admin->id,
            'comment' => $data['comment'],
            'is_private' => $data['is_private'],
        ]);

        if (!$comment->is_private) {
            $feedback->user->notify(new FeedbackUpdatedNotification($feedback, $comment));
        }

        return $feedback;
    }
}