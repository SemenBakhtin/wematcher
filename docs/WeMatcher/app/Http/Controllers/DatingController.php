<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Events\VideoCallEvent;
use App\Events\VideoCallAcceptEvent;
use App\Events\VideoCallRejectEvent;
use App\Events\VideoCallEndEvent;
use App\Events\VideoCallReceiveEvent;
use App\Models\User;
use Auth;

class DatingController extends Controller
{
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

    public function index()
    {
        $users = User::where('id', '!=', $this->user->id)
            ->where('email_verified_at', '!=', '')
            ->whereHas('person', function ($query) {
                $query->where('status', 'active');
            })
            ->paginate(12, ['*'], 'page_user');

        return view('user.index', ['users' => $users]);
    }

    function generateSessionId()
    {
        return uniqid('vc');
    }

    public function call(Request $request, $to)
    {
        event(new VideoCallEvent($this->user, $to));
    }

    public function receive(Request $request, $to)
    {
        event(new VideoCallReceiveEvent($this->user, $to));
    }

    public function end(Request $request, $to)
    {
        event(new VideoCallEndEvent($this->user, $to));
        return redirect()->route('videochat.dating.index');
    }

    public function accept(Request $request, $to)
    {
        $sessionid = $this->generateSessionId();
        event(new VideoCallAcceptEvent($this->user, $to, $sessionid));
        return redirect()->route('videochat.dating.meet', ['sessionid' => $sessionid, 'partner' => $to]);
    }

    public function reject(Request $request, $to)
    {
        event(new VideoCallRejectEvent($this->user, $to));
    }

    public function meet(Request $request, $sessionid, $partner)
    {
        $friends = Friendship::where(['user_id' => $this->user->id, 'friend_id' => $partner])
            ->orWhere(['user_id' => $partner, 'friend_id' => $this->user->id])->count();

        return view('videochat.dating.room', [
            'sessionid'   => $sessionid,
            'partner'               => $partner,
            'showfriend'               => $friends,
            'openvidu_serverurl'    => config('openvidu.serverurl'),
            'openvidu_secret'       =>  config('openvidu.secretkey'),
        ]);
    }
}