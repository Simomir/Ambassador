<?php

namespace App\Providers;

use App\Events\OrderCompletedEvent;
use App\Events\ProductUpdatedEvent;
use App\Listeners\NotifyAdminListener;
use App\Listeners\NotifyAmbassadorListener;
use App\Listeners\ProductUpdatedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductUpdatedEvent::class => [
            ProductUpdatedListener::class
        ],
        OrderCompletedEvent::class => [
            NotifyAdminListener::class,
            NotifyAmbassadorListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
