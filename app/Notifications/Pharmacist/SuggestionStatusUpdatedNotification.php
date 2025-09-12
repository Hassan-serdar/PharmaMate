<?php

namespace App\Notifications\Pharmacist;

use App\Models\MedicineSuggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuggestionStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MedicineSuggestion $suggestion,
        public string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Update on your medicine suggestion: '{$this->suggestion->name}'";

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Hello ' . $notifiable->firstname . ',')
                    ->line($this->message)
                    ->action('View Suggestions', url('/'))
                    ->line('Thank you for your contribution!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'suggestion_id' => $this->suggestion->id,
            'suggestion_name' => $this->suggestion->name,
            'status' => $this->suggestion->status->value,
            'message' => $this->message,
        ];
    }
}
