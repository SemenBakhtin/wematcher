@extends('layouts.app')

@section('content')
<div class="row">
    @each('friend.tile', $friends, 'friend')
</div>
<div class="row justify-content-center mt-4">
    <div class="col-md-12 text-center">
        <div>{{ $friends->links() }}</div>
    </div>
</div>
@endsection
