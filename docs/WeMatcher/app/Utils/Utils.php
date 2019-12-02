<?php

namespace App\Utils;

class Utils{
    public static function beautyDate($date) {
        $yesterday = date('Y-m-d',strtotime("-1 days"));
        $today = date('Y-m-d');

        if($date == $today){
            return __('Today');
        }
        else if($date == $yesterday){
            return __('Yesterday');
        }
        else{
            return $date;
        }
    }
}