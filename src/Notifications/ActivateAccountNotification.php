<?php

namespace Jiannius\Atom\Notifications;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Passwords\PasswordBroker;

class ActivateAccountNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Email $email
     * @param array $cc
     * @return void
     */
    public function __construct()
    {
        //
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
        $url = url(route('password.reset', [
            'token' => app(PasswordBroker::class)->createToken($notifiable),
            'email' => $notifiable->email,
        ]));
        
        return (new MailMessage)
            ->subject('[' . config('app.name') . '] Activate Your Account')
            ->greeting('Hello!')
            ->line('Please click the button below to activate your account.')
            ->action('Activate Account', $url);
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
