@auth
@if(Request::is('message/room*'))
    <contactlist :contacts="{{ json_encode($myfriends) }}" :isroom="true" :to="{{ json_encode($to->toArray()) }}" baseurl="{{ route('message.room', ['to' => '#to#', 'pagecnt' => 0]) }}"></contactlist>
@elseif(Request::is('message*'))
    <contactlist :contacts="{{ json_encode($myfriends) }}" :isroom="true" :to="undefined" baseurl="{{ route('message.room', ['to' => '#to#', 'pagecnt' => 0]) }}"></contactlist>
@else
    <contactlist :contacts="{{ json_encode($myfriends) }}" :isroom="false" :to="undefined" baseurl="{{ route('profile.view', ['id' => '#to#']) }}"></contactlist>
@endif
@endauth

