@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8 text-center">
            <p class="regpanelheader mb-5">
                {{ $person->name }}
            </p>
            <img src="{{ $person->avatar }}">
            <p><span class='flag-icon flag-icon-{{ strtolower($person->country) }}'></span> {{ App\Constants\Constants::$COUNTRY[$person->country] }}, {{ $person->age }}, {{ $person->gender }}</p>
            @if( $person->friend_status == 'active' )
                <a class="profile_action" href="javascript:call({{$person->user->id}}, '{{ $person->name }}', '{{ asset($person->avatar) }}', '{{ route('videochat.dating.call', ['to' => $person->user->id]) }}', '{{ route('videochat.dating.end', ['to' => $person->user->id]) }}', '{{ route('videochat.dating.accept', ['to' => $person->user->id]) }}', '{{ route('videochat.dating.end', ['to' => $person->user->id]) }}')"><i class="fas fa-video" aria-hidden="true"></i></a>
                <a class="profile_action" href="{{ route('message.room', ['to' => $person->user->id, 'pagecnt' => 0]) }}"><i class="fas fa-comments"></i></a>
            @elseif( $person->friend_status == 'pendingin' )
                <p>{{ $person->name }}{{ __(" wants to add you as a friend.") }}</p>
                <a class="profile_action" href="{{ route('friend.addaccept', ['from' => $person->user->id, 'to' => auth()->user()->id]) }}"><i class="fas fa-check-circle"></i></a>
            @elseif( $person->friend_status == 'pending' )
                <a class="profile_action" href="javascript:invite('{{ $person->user->email }}')"><i class="fas fa-user-plus"></i></a>
            @endif
            <a class="profile_action" href="{{ route('friend.addreject', ['from' => $person->user->id, 'to' => auth()->user()->id]) }}"><i class="fas fa-user-times"></i></a>
        </div>
    </div>
</div>
@endsection
