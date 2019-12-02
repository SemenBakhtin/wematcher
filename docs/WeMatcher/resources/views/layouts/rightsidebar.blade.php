<div class="friendlisttitle">
    {{ __("Friend") }}
</div>
<div class="contactlist">
    @include('friend.friendlist')
</div>
<a
    @if(Request::is('videochat/dating/meet*'))
        href="javascript:dating2chat()"
    @else
        href="{{ route('message.index') }}"
    @endif
    >
    <div class="btn-chatpage">
        {{ __("Chat Box") }}
    </div>
</a>

@push('scripts')
    <script>
        function dating2chat(){
            if(datingComponent == undefined) return;
            datingComponent.goChatRoom();
        }
    </script>
@endpush
