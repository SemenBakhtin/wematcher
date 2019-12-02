<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MembershipController extends Controller
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
    
    public function index(){
        return view('membership.index');
    }

    public function update(){

    }
}
