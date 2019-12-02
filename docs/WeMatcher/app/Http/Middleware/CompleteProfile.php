<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CompleteProfile
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
        if (Auth::guard()->check() && (!Auth::guard()->user()->person || Auth::guard()->user()->person->status == "none")) {
            return redirect('/profile/edit');
        }

        return $next($request);
    }
}
