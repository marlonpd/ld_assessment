<?php

namespace App\Listeners;

use App\Event\UserInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendInviteEmail
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
     * @param  UserInvited  $event
     * @return void
     */
    public function handle(UserInvited $event)
    {
        //\Log::info(print_r($event->data['toName'], true));
        $toName = $event->data['toName'];
        $toEmail = $event->data['toName'];
        $fromEmail = config('config.fromEmail');
        $link = config('config.domain') . '/register?code=' . $event->data['code'];
        $data = array(
            'link' => $link,
            'email' => $toEmail
        );

        \Mail::send('mails.user_invite', $data, function($message) use ($toName, $toEmail, $fromEmail) {
            $message->to($toEmail, $toName)
            ->subject('Invitation link');
            $message->from($fromEmail , 'Invitation Link');
        });

    }
}