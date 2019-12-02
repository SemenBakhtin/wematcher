<li class="list-group-item {{ Request::is('message/room*') && $friend->id == $to->id ? 'active' : '' }}">
    <div class="smalltile">
        <a href="{{ Request::is('message*') ? route('message.room', ['to' => $friend->id, 'pagecnt' => 0]) : route('profile.view', ['id' => $friend->id]) }}">
            <img src="{{ $friend->person->avatar }}">
            <div class="smalltile-name">{{ $friend->person->name }}</div>
            <div class="smalltile-status">{{ $friend->status }}</div>
            @if($friend->unreadcnt>0) <div class="unreadcnt">{{ $friend->unreadcnt }}</div> @endif
        </a>
    </div>
</li>