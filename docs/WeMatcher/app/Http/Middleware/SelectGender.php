<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class SelectGender
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard()->check() || !Auth::guard()->user()->person || Auth::guard()->user()->person->status == "none") {
            if (!Session::has('gender'))
            {
                return redirect('/videochat/random/mygender');
            }
        }
        return $next($request);
    }
}
