@extends('layouts.app')

@section('content')
<div
    class="dating_component"
    @auth
        data-logininfo="{{ json_encode(auth()->user()) }}"
    @endauth
    data-openvidu_server_url = "{{ $openvidu_serverurl }}"
    data-openvidu_server_secret = "{{ $openvidu_secret }}"
    data-openvidu_session_id="{{ $sessionid }}"
    data-lang="{{ app()->getLocale() }}"
    data-endurl="{{ route('videochat.dating.end', ['to' => $partner]) }}"
    data-translateurl="{{ url('/translate') }}"
    data-isfriend="{{ $showfriend }}"
    data-readmsgtransurl="{{ route('message.readwithtrans') }}"
    data-readmsgurl="{{ route('message.readwithnotrans') }}"
    data-sendmsgurl="{{ route('message.sendbyemail') }}"
    data-chatroomurl="{{ route('message.room', ['to' => $partner, 'pagecnt' => 0]) }}"
    >
</div>
@endsection
