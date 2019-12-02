<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function friend()
    {
        return $this->hasOne('App\Models\User', 'id', 'friend_id');
    }
}
