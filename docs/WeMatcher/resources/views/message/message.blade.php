<div class="row justify-content-center mt-1">
    <div class="col-md-12">
        <div class="chatmessage @if( $message->from == $to->id ) you @else me @endif">
            @if( $message->from == $to->id )
                <div class="avatar">
                    <img src="{{ $to->person->avatar }}">
                </div>
            @endif
            <p class="mb-1">
                <span class="time">
                    @if( $message->from == $to->id ) {{ $to->person->name }},  @endif
                    {{ date('H:i', strtotime($message->created_at)) }}
                </span>
            </p>
            <div class="text">
                @if( $message->translated && $message->from == $to->id && $message->translated_message != $message->message)
                    {{ $message->translated_message }}
                    <br>
                    <span class="original">
                        {{ $message->message }}
                    </span>
                @else
                    {{ $message->message }}
                @endif
            </div>
        </div>
    </div>
</div>