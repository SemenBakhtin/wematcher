<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\VideoCallAcceptEvent' => [
            'App\Listeners\VideoMessageListener',
        ],
        'App\Events\VideoCallEndEvent' => [
            'App\Listeners\VideoMessageListener',
        ],
        'App\Events\VideoCallEvent' => [
            'App\Listeners\VideoMessageListener',
        ],
        'App\Events\VideoCallReceiveEvent' => [
            'App\Listeners\VideoMessageListener',
        ],
        'App\Events\VideoCallRejectEvent' => [
            'App\Listeners\VideoMessageListener',
        ],
        'App\Events\MessageEvent' => [
            'App\Listeners\VideoMessageListener',
        ],
        'SocialiteProviders\Manager\SocialiteWasCalled' => [
            'SocialiteProviders\\VKontakte\\VKontakteExtendSocialite@handle',
            'SocialiteProviders\\Google\\GoogleExtendSocialite@handle',
            'SocialiteProviders\\Facebook\\FacebookExtendSocialite@handle',
            'SocialiteProviders\\Weixin\\WeixinExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
