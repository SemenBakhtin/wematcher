<div class="col-md-3 col-sm-4 col-6">
    <div class="tile">
        <img src="{{ $user->person->avatar }}">
        <p class="mb-0"><a href="{{ route('profile.view', ['id' => $user->id]) }}">{{ $user->person->name }}</a> <span class='flag-icon flag-icon-{{ strtolower($user->person->country) }}'></span></p>
        <p class="mb-0">{{ $user->person->age }}, {{ $user->person->gender }}</p>
        <a class="profile_action" href="javascript:call({{$user->id}}, '{{ $user->person->name }}', '{{ asset($user->person->avatar) }}', '{{ route('videochat.dating.call', ['to' => $user->id]) }}', '{{ route('videochat.dating.end', ['to' => $user->id]) }}', '{{ route('videochat.dating.accept', ['to' => $user->id]) }}', '{{ route('videochat.dating.end', ['to' => $user->id]) }}')"><i class="fas fa-video" aria-hidden="true"></i></a>
        <a class="profile_action" href="{{ route('message.room', ['to' => $user->id, 'pagecnt' => 0]) }}"><i class="fas fa-comments"></i></a>
    </div>
</div>
