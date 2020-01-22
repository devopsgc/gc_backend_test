<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InstagramStatsFailed extends Notification
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
            ->subject('Instagram stats update failed.')
            ->greeting('Instagram stats update failed.')
            ->line(url('https://www.instagram.com/'.$this->record->instagram_id))
            ->line($this->e->getMessage())
            ->action('View Record', url('records/'.$this->record->id));
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
