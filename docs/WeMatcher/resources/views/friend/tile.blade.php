<div class="col-md-3 col-sm-4 col-6">
    <div class="tile">
        <img src="{{ $friend->friend->person->avatar }}">
        <p class="mb-0"><a href="{{ route('profile.view', ['id' => $friend->friend->id]) }}">{{ $friend->friend->person->name }}</a> <span class='flag-icon flag-icon-{{ strtolower($friend->friend->person->country) }}'></span></p>
        <p class="mb-0">{{ $friend->friend->person->age }}, {{ $friend->friend->person->gender }}</p>
        <a class="profile_action" href="javascript:call({{$friend->friend->id}}, '{{ $friend->friend->person->name }}', '{{ asset($friend->friend->person->avatar) }}', '{{ route('videochat.dating.call', ['to' => $friend->friend->id]) }}', '{{ route('videochat.dating.end', ['to' => $friend->friend->id]) }}', '{{ route('videochat.dating.accept', ['to' => $friend->friend->id]) }}', '{{ route('videochat.dating.end', ['to' => $friend->friend->id]) }}')"><i class="fas fa-video" aria-hidden="true"></i></a>
        <a class="profile_action" href="{{ route('message.room', ['to' => $friend->friend->id, 'pagecnt' => 0]) }}"><i class="fas fa-comments"></i></a>
    </div>
</div>
