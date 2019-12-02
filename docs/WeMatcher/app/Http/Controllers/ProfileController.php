<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Auth;
use App\Models\Person;
use App\Models\Friendship;
use App\Http\Requests\PersonRequest;
use Storage;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            $this->person = Auth::user()->person;
            $this->user = Auth::user();
            View::share('person', $this->person);
            $this->friends();
            return $next($request);
        });
    }

    public function index()
    {
        if($this->person->status == 'none'){
            return redirect()->route('profile.edit');
        }
        return view('profile.index');
    }

    public function view($id)
    {
        $person = Person::where('user_id', $id)->first();
        $friendship = Friendship::where('friend_id', $this->user->id)->where('user_id', $id)->first();
        if($friendship){
            if($friendship->status == 'active'){
                $person->friend_status = 'active';
            }
            else{
                $person->friend_status = 'pendingin';
            }
        }
        else{
            $person->friend_status = 'pending';
        }
        return view('profile.view', ['person' => $person]);
    }

    public function edit()
    {
        return view('profile.edit');
    }

    public function update(PersonRequest $request)
    {
        $image = $request->avatar;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(10).'.'.'png';

        $filepath = 'avatar/'.$request->id.'.png';
        Storage::disk('public')->put($filepath, base64_decode($image));
         
        $person = Person::find($request->id);
        $person->avatar = '/storage/'.$filepath;
        $person->name = $request->name;
        $person->country = $request->country;
        $person->age = $request->age;
        // $person->birthday = $request->birthday;
        $person->gender = $request->gender;
        $person->status = 'active';
        $person->save();
        return redirect('/profile');
    }

    public function get(Request $request, $id){
        $user = User::find($id);
        return $user->toJson();
    }
}
