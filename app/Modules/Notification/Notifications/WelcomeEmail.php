<?php

namespace App\Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmail extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Log notification to both database and mail channels
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to QuillNova AI!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for registering at QuillNova, the premier AI content & productivity SaaS platform.')
            ->line('You have been granted 50 free AI Requests and 20,000 generated words daily.')
            ->action('Get Started Now', url('/dashboard'))
            ->line('We are excited to see what you will create!');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to QuillNova!',
            'message' => 'Thank you for registering. You have 50 requests and 20,000 words daily free.',
            'link' => '/dashboard',
        ];
    }
}
