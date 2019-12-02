<?php

namespace App\Http\Middleware;

use Closure;

class EnsurePremium
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
        if(auth()->check() && auth()->user()->person && auth()->user()->person->status=='active' && auth()->user()->person->membership_plan>0){
            return redirect()->route('membership.index');
        }
        return $next($request);
    }
}
