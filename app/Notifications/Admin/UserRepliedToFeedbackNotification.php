<?php

namespace App\Notifications\Admin;

use App\Models\Feedback;
use App\Models\FeedbackComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRepliedToFeedbackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Feedback $feedback,
        public FeedbackComment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $this->comment->user;
        $subject = "New Reply on Ticket #{$this->feedback->id}: '{$this->feedback->subject}'";

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Hello ' . $notifiable->firstname . ',')
                    ->line("The user {$user->firstname} {$user->lastname} has replied to a feedback ticket.")
                    ->line("Reply: '{$this->comment->comment}'")
                    ->action('View Ticket', url('feedback/' . $this->feedback->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'feedback_id' => $this->feedback->id,
            'feedback_subject' => $this->feedback->subject,
            'user_name' => $this->comment->user->firstname,
            'message' => 'A user has replied to a feedback ticket.',
        ];
    }
}