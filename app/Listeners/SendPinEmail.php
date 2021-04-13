<?php

namespace App\Listeners;

use App\Event\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPinEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $toName = $event->data['toName'];
        $toEmail = $event->data['toEmail'];
        $fromEmail = config('config.fromEmail');
        $data = array(
            'pin' => $event->data['pin'],
            'email' => $toEmail,
        );

        \Mail::send('mails.user_pin', $data, function($message) use ($toName, $toEmail, $fromEmail) {
            $message->to($toEmail, $toName)
            ->subject('User Registration PIN');
            $message->from($fromEmail , 'User Registration PIN');
        });
    }
}