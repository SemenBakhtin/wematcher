<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $urls = [];
        foreach (Storage::disk('public')->files('photos_carousel') as $filename) {
            $urls[] = asset(Storage::disk('public')->url($filename));
        }

        return view('home', ['photoUrls' => json_encode($urls)]);
    }
}
