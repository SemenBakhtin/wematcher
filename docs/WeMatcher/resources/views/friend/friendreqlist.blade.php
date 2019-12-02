@auth
<ul class="list-group">
    @if(isset($myfriends))
        @each('friend.smalltile', $invites, 'friend')
    @endif
</ul>
@endauth
