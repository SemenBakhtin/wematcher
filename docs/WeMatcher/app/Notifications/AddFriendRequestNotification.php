<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Components\UserListItemComponent;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AddFriendRequestNotification extends Notification
{
    use Queueable;

    public $from;
    public $to;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
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
            ->subject($this->from->person->name . ' ' . __('via') . ' ' . config('app.name'))
            ->greeting($this->to->person->name)
            ->line($this->from->person->name . __(' wants to add you as a friend.'))
            ->action(__('Accept'), route('friend.addaccept', ['from' => $this->from->id, 'to' => $this->to->id]))
            ->line(__('Thank you for using our application!'))
            ->markdown('mail.friendemail', ['users' => [$this->from]]);
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
            'from' => $this->from->id,
            'to' => $this->to->id
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'from' => $this->from->id,
            'from_name' => $this->from->person->name
        ]);
    }
}