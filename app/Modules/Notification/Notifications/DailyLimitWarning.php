<?php

namespace App\Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DailyLimitWarning extends Notification
{
    use Queueable;

    protected string $type; // 'approaching' or 'exceeded'
    protected int $usageCount;
    protected int $limitCount;

    public function __construct(string $type, int $usageCount, int $limitCount)
    {
        $this->type = $type;
        $this->usageCount = $usageCount;
        $this->limitCount = $limitCount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;

        if ($this->type === 'exceeded') {
            $mail->subject('Daily AI Limit Exceeded - QuillNova')
                ->greeting('Hello ' . $notifiable->name)
                ->line('This is a notification to let you know that you have reached today\'s free usage limit.')
                ->line("Current usage: {$this->usageCount} units out of a limit of {$this->limitCount}.")
                ->line('Your limits will automatically reset at midnight. Please come back tomorrow!')
                ->action('View Usage History', url('/history'));
        } else {
            $mail->subject('Daily AI Limit Alert - QuillNova')
                ->greeting('Hello ' . $notifiable->name)
                ->line('You are approaching your daily free usage limit.')
                ->line("You have used {$this->usageCount} out of {$this->limitCount} units.")
                ->action('View Dashboard', url('/dashboard'));
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        if ($this->type === 'exceeded') {
            return [
                'title' => 'Daily Free Limit Reached',
                'message' => "You have used {$this->usageCount} of {$this->limitCount} units. Reset occurs at midnight.",
                'link' => '/dashboard',
            ];
        }

        return [
            'title' => 'Approaching Daily Limit',
            'message' => "You have used {$this->usageCount} out of {$this->limitCount} daily units.",
            'link' => '/dashboard',
        ];
    }
}
