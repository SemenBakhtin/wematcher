@extends('layouts.app')

@section('content')
<div class="row">
    @each('user.tile', $users, 'user')
</div>
<div class="row justify-content-center mt-4">
    <div class="col-md-12 text-center">
        <div>{{ $users->links() }}</div>
    </div>
</div>
@endsection
