<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Mail\Message;
use Mail;

class NotifyAmbassadorListener
{
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;

        Mail::send('ambassador', ['order' => $order], function (Message $message) use ($order) {
            $message->subject('Order has been completed');
            $message->to($order->ambassador_email);
        });
    }
}
