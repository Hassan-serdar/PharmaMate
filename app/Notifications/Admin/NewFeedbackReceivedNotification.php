<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Feedback;

class NewFeedbackReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Feedback $feedback)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // إيميل + إشعار داخل لوحة التحكم
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $this->feedback->user;
        $subject = "New Feedback Received: '{$this->feedback->subject}'";

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Hello ' . $notifiable->firstname . ',')
                    ->line("A new feedback ticket has been submitted by {$user->firstname} {$user->lastname}.")
                    ->line("Subject: {$this->feedback->subject}")
                    ->action('View Ticket', url('feedback/' . $this->feedback->id))
                    ->line('Please review it at your earliest convenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'feedback_id' => $this->feedback->id,
            'feedback_subject' => $this->feedback->subject,
            'user_name' => $this->feedback->user->firstname,
            'message' => 'A new feedback ticket has been submitted.',
        ];
    }
}