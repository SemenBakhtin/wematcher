<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\AddFriendRequestNotification;
use App\Notifications\AddFriendRequestAcceptNotification;
use App\Notifications\AddFriendRequestRejectNotification;
use Auth;
use View;

class FriendController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled']);
        $this->middleware(function ($request, $next) {
            $this->person = Auth::user()->person;
            $this->user = Auth::user();
            $this->friends();
            return $next($request);
        });
    }

    public function index() {
        $friends = Friendship::where('user_id', $this->user->id)->where('status', 'active')->paginate(12, ['*'], 'page_friend');
        return view('friend.index', ['friends' => $friends]);
    }

    public function invites() {
        return view('friend.invites');
    }

    public function addRequest(Request $request){
        $friend_user = User::where('email', $request->friend)->first();
        $count = Friendship::where('user_id', $this->user->id)->where('friend_id', $friend_user->id)->where('status', 'active')->count();
        if ($count > 0) {
            return response()->json(['success' => false, 'error' => 'exist']);
        }

        $count = Friendship::where('user_id', $friend_user->id)->where('friend_id', $this->user->id)->where('status', 'pending')->count();
        if ($count > 0) {
            return response()->json(['success' => false, 'error' => 'pending']);
        }

        $friendship = Friendship::where('user_id', $this->user->id)->where('friend_id', $friend_user->id)->first();

        if(!$friendship){
            $friendship = new Friendship;
            $friendship->user_id = $this->user->id;
            $friendship->friend_id = $friend_user->id;
            $friendship->status = 'pending';
            $friendship->last_contact_at = date('Y-m-d H:i:s');
            $friendship->save();
        }

        $friend_user->notify(new AddFriendRequestNotification($this->user, $friend_user));

        return response()->json([
            'success' => true,
        ]);
    }

    public function addAccept(Request $request, $from, $to){
        //to = me

        $from_user = User::find($from);
        $friendship = Friendship::where('user_id', $from)->where('friend_id', $to)->first();
        $friendship->status = 'active';
        $friendship->last_contact_at = date('Y-m-d H:i:s');
        $friendship->save();

        $count = Friendship::where('user_id', $to)->where('friend_id', $from)->count();

        if($count==0){
            $friendship_ = new Friendship;
            $friendship_->user_id = $to;
            $friendship_->friend_id = $from;
            $friendship_->status = 'active';
            $friendship_->last_contact_at = date('Y-m-d H:i:s');
            $friendship_->save();

            $from_user->notify(new AddFriendRequestAcceptNotification($this->user, $from_user));
        }

        return redirect()->route('profile.view', ['id' => $from]);
    }
    
    public function addReject(Request $request, $from, $to){
        //to = me

        $from_user = User::find($from);
        Friendship::where('user_id', $from)->where('friend_id', $to)->delete();
        Friendship::where('user_id', $to)->where('friend_id', $from)->delete();
        $from_user->notify(new AddFriendRequestRejectNotification($this->user, $from_user));

        return redirect()->route('profile.view', ['id' => $from]);
    }
}
