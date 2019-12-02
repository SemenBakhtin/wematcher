<?php

namespace App\Components;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Htmlable;

class UserListItemComponent implements Htmlable
{
    /** \Illuminate\Http\Request */
    private $request;

    /** @var string */
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function toHtml(): string
    {
        return view('components.userlistitem', [
            'avatar' => $this->user->person->avatar,
            'name' => $this->user->person->name,
        ]);
    }
}