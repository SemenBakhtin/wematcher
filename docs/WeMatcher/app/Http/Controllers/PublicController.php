<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class PublicController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->friends();
            return $next($request);
        });
    }

    public function privacypolicy()
    {
        return view('public.privacypolicy');
    }

    public function termsofservice()
    {
        return view('public.termsofservice');
    }

    public function cookiepolicy()
    {
        return view('public.cookiepolicy');
    }
}