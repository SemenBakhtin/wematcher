<div class="col-md-3 col-sm-4 col-6">
    <div class="tile">
        <img src="{{ $invite->person->avatar }}">
        <p class="mb-0"><a href="{{ route('profile.view', ['id' => $invite->id]) }}">{{ $invite->person->name }}</a> <span class='flag-icon flag-icon-{{ strtolower($invite->person->country) }}'></span></p>
        <p class="mb-0">{{ $invite->person->age }}, {{ $invite->person->gender }}</p>
        <a class="profile_action" href="{{ route('friend.addaccept', ['from' => $invite->person->user->id, 'to' => auth()->user()->id]) }}"><i class="fas fa-check-circle" aria-hidden="true"></i></a>
        <a class="profile_action" href="{{ route('friend.addreject', ['from' => $invite->person->user->id, 'to' => auth()->user()->id]) }}"><i class="fas fa-user-times" aria-hidden="true"></i></a>
    </div>
</div>
