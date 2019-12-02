@extends('layouts.app')

@section('content')
<div class="row">
    @each('friend.invitetile', $invites, 'invite')
</div>
@endsection
