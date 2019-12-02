<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Models\User;
use App\Models\Person;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        if($provider == 'facebook'){
            return Socialite::driver($provider)->asPopup()->redirect();    
        }
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $getInfo = Socialite::driver($provider)->user(); 
        $user = $this->createUser($getInfo,$provider); 
        auth()->login($user);
        return view('auth.socialaftercallback');
        // return redirect()->to('/home');
    }

    function createUser($getInfo,$provider){
        
        $user = User::where('provider_id', $getInfo->id)->first();
        $email = $getInfo->email;
        if($email == NULL){
            if($provider == 'vkontakte'){
                $email = $getInfo->accessTokenResponseBody['email'];
            }
        }
        if (!$user) {
            $olduser = User::where('email', $email)->first();
            if($olduser){
                $olduser->provider = $provider;
                $olduser->provider_id = $getInfo->id;
                $olduser->email_verified_at = date('Y-m-d H:i:s');
                $olduser->save();
            }
            else{
                $user = User::create([
                    'email'    => $email,
                    'password'  => md5(rand(1,10000)),
                    'provider' => $provider,
                    'provider_id' => $getInfo->id
                ]);

                $user->email_verified_at = $user->created_at;
                $user->save();

                $person = new Person;
                $person->user_id = $user->id;
                $person->name = $getInfo->name;
                $person->save();
            }
        }
        return $user;
    }
}
