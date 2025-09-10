<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FeedbackUpdatedNotification;
use App\Notifications\Admin\NewFeedbackReceivedNotification;

class FeedbackService
{
    public function store(User $user, array $data, array $files = []): Feedback
    {
        $feedback = $user->feedback()->create($data);

        foreach ($files as $file) {
            $path = $file->store('feedback-attachments', 'public');
            $feedback->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
        
        $admins = User::whereIn('role', [Role::ADMIN, Role::SUPER_ADMIN])->get();
        dd($admins); 

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewFeedbackReceivedNotification($feedback));
        }

        return $feedback;
    }

    /**
     * تحديث بيانات الشكوى من قبل الأدمن وإرسال الإشعارات اللازمة
     */
    public function updateFeedbackByAdmin(Feedback $feedback, array $data): Feedback
    {
        $oldStatus = $feedback->status;

        $feedback->update($data);

        if (isset($data['status']) && $data['status'] !== $oldStatus->value) {
             $feedback->user->notify(new FeedbackUpdatedNotification($feedback));
        }
        
        return $feedback;
    }
    public function updateFeedbackByUser(Feedback $feedback, array $data): Feedback
    {
        $feedback->update($data);
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