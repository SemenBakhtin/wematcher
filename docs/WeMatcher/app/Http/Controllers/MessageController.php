<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Constants\Constants;
use Auth;
use DB;
use App\Events\MessageEvent;
use Session;
use View;

class MessageController extends Controller
{
    protected $autotranslation;
    protected $language;
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

            $this->autotranslation = true;
            if (Session::has('autotranslation')) {
                $this->autotranslation = session('autotranslation');
            }

            if (Session::has('language')) {
                $this->language = session('language');
            } else {
                $this->language = app()->getLocale();
            }
            View::share(['autotranslation' => $this->autotranslation, 'language' => $this->language]);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        return view('message.index');
    }

    public function room(Request $request, $to, $pagecnt = 0)
    {
        $to_user = User::find($to);

        $from = $this->user->id;
        $unreads = Message::where('from', $to)->where('to', $from)->where('read', 0)->get();
        foreach ($unreads as $message) {
            if ($this->autotranslation) {
                $curl = curl_init();
                $headers = array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                );
                $text = curl_escape($curl, $message->message);
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_URL => config('translate.google.scripturl') . '?text=' . $text . '&lang=' . $this->language
                ]);
                if ($result = curl_exec($curl)) {
                    try {
                        $result = json_decode($result);
                        $result = json_decode($result);
                        if ($result->success) {
                            $message->translated_message = $result->translatedText;
                            $message->lang = $this->language;
                            $message->translated = true;
                        }
                    } catch (\Exception $e) { }
                }
            }
            $message->read = 1;
            $message->save();
        }

        $messages = Message::where(function ($query) use ($from, $to) {
            $query->where('from', $from)
                ->where('to', $to);
        })
            ->orWhere(function ($query) use ($from, $to) {
                $query->where('to', $from)
                    ->where('from', $to);
            })->orderBy('created_at', 'desc')->take(($pagecnt + 1) * Constants::$MSGCNT)->get();

        $date = '';
        $messages_ = [];
        foreach ($messages->reverse() as $message) {
            $curdate = date('Y-m-d', strtotime($message->created_at));
            if ($date != $curdate) {
                $messages_[$curdate] = [];
            }
            $messages_[$curdate][] = $message;
            $date = $curdate;
        }

        return view('message.room', ['to' => $to_user, 'me' => auth()->user(), 'messages' => $messages_, 'pagecnt' => $pagecnt]);
    }

    public function send(Request $request)
    {
        $to = $request->to;
        $type = $request->type;
        $msg = $request->message;

        $message = new Message;
        $message->from = $this->user->id;
        $message->to = $to;
        $message->type = $type;
        $message->message = $msg;
        $message->translated = false;
        $message->save();

        event(new MessageEvent($this->user, $to, $message));

        $messageHtml = view('message.message', ['message' => $message, 'to' => User::find($to)])->render();

        return response()->json(['result' => 'ok', 'msgid' => $message->id, 'messageHtml' => $messageHtml]);
    }

    public function sendbyemail(Request $request)
    {
        $to = $request->to;
        $touser = User::where('email', $to)->first();
        $to = $touser->id;
        $type = $request->type;
        $msg = $request->message;

        $message = new Message;
        $message->from = $this->user->id;
        $message->to = $to;
        $message->type = $type;
        $message->message = $msg;
        $message->translated = false;
        $message->save();

        event(new MessageEvent($this->user, $to, $message));

        $messageHtml = view('message.message', ['message' => $message, 'to' => $touser])->render();

        return response()->json(['result' => 'ok', 'msgid' => $message->id]);
    }

    public function sendasread(Request $request, $to, $type, $msg)
    { }

    public function read(Request $request, $msgid)
    {
        $message = Message::find($msgid);
        $message->read = 1;
        if ($this->autotranslation) {
            $curl = curl_init();
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
            );
            $text = curl_escape($curl, $message->message);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_URL => config('translate.google.scripturl') . '?text=' . $text . '&lang=' . $this->language
            ]);
            if ($result = curl_exec($curl)) {
                try {
                    $result = json_decode($result);
                    $result = json_decode($result);
                    if ($result->success) {
                        $message->translated_message = $result->translatedText;
                        $message->lang = $this->language;
                        $message->translated = true;
                    }
                } catch (\Exception $e) { }
            }
        }
        $message->save();

        $from_user = User::find($message->from);
        return view('message.message', ['message' => $message, 'to' => $from_user])->render();
    }

    public function readwithnotrans(Request $request)
    {
        $message = Message::find($request->msgid);
        $message->read = 1;
        $message->save();
    }

    public function readwithtrans(Request $request)
    {
        $message = Message::find($request->msgid);
        $message->read = 1;
        $message->lang = $request->lang;
        $message->translated_message = $request->translated_message;
        $message->translated = true;
        $message->save();
    }

    public function translateconf(Request $request, $lang, $auto)
    {
        if ($auto == 'true') {
            $auto = true;
        } else {
            $auto = false;
        }

        session()->put('autotranslation', $auto);
        session()->put('language', $lang);
        return redirect()->back();
    }
}