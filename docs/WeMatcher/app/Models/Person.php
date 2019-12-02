<?php

namespace App\Models;

use App\Traits\Enums;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use Enums;

    protected $enumGenders = [
        'Male',
        'Female',
        'Couple',
        'Trans'
    ];

    protected $enumAges = [
        '18-24',
        '25-34',
        '35-44',
        '45-54',
        '55-64',
        '65+'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
