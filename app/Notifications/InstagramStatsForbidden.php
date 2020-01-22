<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InstagramStatsForbidden extends Notification
{
    use Queueable;

    public $record;
    public $e;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($record, $e)
    {
        $this->record = $record;
        $this->e = $e;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Instagram stats update forbidden.')
            ->greeting('Instagram stats update forbidden.')
            ->line(url('records/'.$this->record->id))
            ->line($this->e->getMessage())
            ->action('Update Instagram Authentication', url('instagram'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
