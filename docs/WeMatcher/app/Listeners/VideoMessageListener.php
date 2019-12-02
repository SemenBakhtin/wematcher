<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Friendship;

class VideoMessageListener
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $friendship = Friendship::where('user_id', $event->from->id)->where('friend_id', $event->to)->first();
        if($friendship)
        {
            $friendship->last_contact_at = date('Y-m-d H:i:s');
            $friendship->save();
        }

        $friendship = Friendship::where('friend_id', $event->from->id)->where('user_id', $event->to)->first();
        if($friendship)
        {
            $friendship->last_contact_at = date('Y-m-d H:i:s');
            $friendship->save();
        }
    }
}
