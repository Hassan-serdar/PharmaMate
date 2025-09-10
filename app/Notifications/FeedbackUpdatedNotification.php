<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Feedback;
use App\Models\FeedbackComment;

// Using ShouldQueue ensures notifications are sent in the background
// This makes your application feel faster for the user.
class FeedbackUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     * We receive the feedback ticket, and optionally, a comment from the admin.
     */
    public function __construct(
        public Feedback $feedback,
        public ?FeedbackComment $comment = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; 
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Update regarding your ticket: '{$this->feedback->subject}'";

        // We determine the main message based on whether an admin replied
        // or just changed the status.
        $line = $this->comment
            ? "An administrator has replied to your ticket: '{$this->comment->comment}'"
            : "The status of your ticket has been updated to '{$this->feedback->status->value}'.";

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Hello ' . $notifiable->firstname . ',')
                    ->line($line)
                    ->action('View Details', url('/')) 
                    ->line('Thank you for using our application!');
    }
    
    /**
     * Get the array representation of the notification (for database storage).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'feedback_id' => $this->feedback->id,
            'feedback_subject' => $this->feedback->subject,
            'message' => $this->comment ? 'You have a new reply from an administrator.' : 'The status of your feedback has been updated.',
        ];
    }
}

