<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Session;
use View;

use Stopka\OpenviduPhpClient\OpenVidu;

class RandomChatController extends Controller
{
    private $gender = 'Male';
    private $name = 'unknown';
    private $email = 'unknown';
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                $this->user = auth()->user();
                $this->person = $this->user->person;
                $this->email = auth()->user()->email;
            }

            if (auth()->check() && auth()->user()->person && auth()->user()->person->status == 'active') {
                $this->gender = auth()->user()->person->gender;
                $this->name = auth()->user()->person->name;
            } else {
                if (Session::has('gender')) {
                    $this->gender = session('gender');
                }
            }

            $urls = [];
            foreach (Storage::disk('public')->files('photos_carousel') as $filename) {
                $urls[] = asset(Storage::disk('public')->url($filename));
            }

            View::share([
                'websocket_url'         => config('websocket.websocketurl'),
                'email'                 => $this->email,
                'name'                  => $this->name,
                'gender'                => $this->gender,
                'openvidu_serverurl'    => config('openvidu.serverurl'),
                'openvidu_secret'       => config('openvidu.secretkey'),
                'photoUrls'             => json_encode($urls)
            ]);
            $this->friends();
            return $next($request);
        });
    }

    public function index()
    {
        return view('videochat.random.index', ['step' => 0, 'vgender' => 'any', 'status' => 'unconnected']);
    }

    public function mygenderSelect()
    {
        return view('videochat.random.gender');
    }

    public function mygenderUpdate($gender)
    {
        session(['gender' => $gender]);
        return redirect()->route('videochat.random.index');
    }

    public function yourgenderSelect()
    {
        return view('videochat.random.yourgender');
    }

    public function yourgenderUpdate($gender)
    {
        return view('videochat.random.index', ['step' => 1, 'vgender' => $gender, 'status' => 'finding']);
    }
}