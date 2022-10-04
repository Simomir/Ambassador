<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminListener
{
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;
    }
}
