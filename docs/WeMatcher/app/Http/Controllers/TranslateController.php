<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Translate\TranslateClient;

class TranslateController extends Controller
{
    public function translate(Request $request)
    {
        $curl = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );
        $text = curl_escape($curl, $request->text);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => config('translate.google.scripturl') . '?text=' . $text . '&lang=' . $request->lang
        ]);
        if ($result = curl_exec($curl)) {
            return response(json_decode($result), 200);
        }
        return response(curl_error($curl), 400);
    }
}