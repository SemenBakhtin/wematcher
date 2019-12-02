<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Friendship;
use App\Models\Message;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $person;
    protected $user;

    public function friends()
    {
        if (auth()->check()) {
            $friends = [];
            $friendships = Friendship::where('user_id', $this->user->id)->orderBy('last_contact_at', 'desc')->get();
            foreach ($friendships as $friendship) {
                $friend = $friendship->friend;
                $friend->personinfo = $friend->person;
                $friend->status = $friendship->status;

                $unreadcnt = Message::where('from', $friend->id)->where('to', $this->user->id)->where('read', 0)->count();
                $friend->unreadcnt = $unreadcnt;
                $friend->onlinestatus = false;

                $friends[] = $friend;
            }

            $invites = [];
            $friendships = Friendship::where('friend_id', $this->user->id)->where('status', 'pending')->orderBy('last_contact_at', 'desc')->get();
            foreach ($friendships as $friendship) {
                $friend = $friendship->user;
                $friend->status = $friendship->status;
                $invites[] = $friend;
            }
            View::share(['myfriends' => $friends, 'invites' => $invites]);
        }
    }
}