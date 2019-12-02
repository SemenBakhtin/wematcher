@extends('layouts.app')

@section('content')
<div 
    class="random_video_component" 
    data-photourls="{{ $photoUrls }}" 
    @auth
        data-logininfo="{{ json_encode(auth()->user()) }}"
        data-isloggedIn="1"
    @else
        data-isloggedIn="0"
    @endauth
    data-step="{{ $step }}"
    data-websocketurl="{{ $websocket_url }}"
    data-gender="{{ $gender }}"
    data-vgender="{{ $vgender }}"
    data-openvidu_server_url = "{{ $openvidu_serverurl }}"
    data-openvidu_server_secret = "{{ $openvidu_secret }}"
    data-genderurl="{{ route('videochat.random.yourgender') }}"
    data-friendrequesturl="{{ route('friend.addrequest') }}"
    data-status="{{ $status }}"
    data-lang="{{ app()->getLocale() }}"
    data-translateurl="{{ url('/translate') }}"
    data-readmsgtransurl="{{ route('message.readwithtrans') }}"
    data-readmsgurl="{{ route('message.readwithnotrans') }}"
    data-sendmsgurl="{{ route('message.sendbyemail') }}"
    >
</div>
@endsection
