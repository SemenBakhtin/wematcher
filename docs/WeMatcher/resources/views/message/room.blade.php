@extends('layouts.app')

@section('content')
<div class="chatwrapper">
    <div class="chatbox">
        @foreach($messages as $date=>$messages_ )
            <div class="row justify-content-center mt-1">
                <div class="col-md-12 text-center">
                    - {{ \App\Utils\Utils::beautyDate($date) }} -
                </div>
            </div>
            @foreach($messages_ as $message)
                @include('message.message', ['message' => $message, 'to' => $to])
            @endforeach
        @endforeach
    </div>
    <div onclick="bottomFunction()" class="scrollbottom" title="Go to bottom"><i class="fas fa-chevron-down" aria-hidden="true"></i></div>
</div>
<input type="hidden" id="pagecnt" value="{{ $pagecnt }}">
<input type="hidden" name="to" value="{{ $to->id }}">
<div class="inputwrapper">
    <img class="sellangbtn" src="{{ asset('img/mob_language.png') }}" class="lang_img"/>
    <i class="fas fa-check text-success" aria-hidden="true" id="trans_auto"></i>
    <input type="text" name="message" id="messageinput">
    <a href="javascript:call({{$to->id}}, '{{ $to->person->name }}', '{{ asset($to->person->avatar) }}', '{{ route('videochat.dating.call', ['to' => $to->id]) }}', '{{ route('videochat.dating.end', ['to' => $to->id]) }}', '{{ route('videochat.dating.accept', ['to' => $to->id]) }}', '{{ route('videochat.dating.end', ['to' => $to->id]) }}')">
        <div class="btn-send">
            <i class="fas fa-video" aria-hidden="true"></i>
        </div>
    </a>
    <a href="javascript:sendmsg()">
        <div class="btn-send">
            <i class="fas fa-paper-plane"></i>
        </div>
    </a>
</div>

<div
    id="select_language_dlg"
    data-translateauto="{{ $autotranslation}}"
    data-translatelang="{{ $language }}"
    data-translateconfurl="{{ route('message.translateconf', ['lang' => '#lang#', 'autotranslation' => '#auto#']) }}"
>
</div>
@endsection

@section('script')
<script>
    function showLanguageDlg(){
        selectlanguageComponent.close();
    }

    function sendmsg(){
        var message = $('#messageinput').val();
        if(message == ''){
            return;
        }
        $.ajax({
            url: "{{ url('message/send') }}",
            data: {'to': {{$to->id}}, 'message': message, 'type': 'text'},
            success: function( response ){
                if( response.result == 'ok'){
                    $('#messageinput').val('');
                    $('.chatbox').append(response.messageHtml);
                    bottomFunction();
                }
            }
        });
    }

    // When the user clicks on the button, scroll to the bottom of the chatbox
    function bottomFunction() {
        $('.chatbox')[0].scrollTop = $('.chatbox')[0].scrollHeight - $('.chatbox').height(); // For Safari
        // document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    $(document).ready(function() {

        @if(!$autotranslation)
            $('#trans_auto').hide();
            $('#messageinput').width($('#messageinput').width()+20);
        @endif

        $('.sellangbtn').click( function() {
            window.selectlanguageComponent.openDlg();
        })

        var scrollPos = 0;

        function scrollFunction() {
            if( $('.chatbox')[0].scrollHeight - $('.chatbox')[0].scrollTop > $('.chatbox').height() * 2 ) {
                $('.scrollbottom').show();
            } else {
                $('.scrollbottom').hide();
            }

            if($('.chatbox')[0].scrollTop == 0){
                window.location = "{{ route('message.room', ['to' => $to->id, 'pagecnt' => $pagecnt+1]) }}";
            }
            scrollPos = $('.chatbox')[0].scrollTop;
        }

        // When the user scrolls down 20px from the top of the document, show the button
        $('.chatbox').scroll( function() {
            // console.log($('.chatbox')[0].scrollTop + " " + $('.chatbox')[0].scrollHeight);
            scrollFunction();
        })

        $('#messageinput').keypress( function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                sendmsg();
            }
        });

        Echo.private('Message.{{ $me->id }}')
            .listen('.Message', (e) => {
                $.ajax({
                    url: "{{ url('message/read') }}" + "/" + e.message.id,
                    success: function(html) {
                        $('.chatbox').append(html);
                        bottomFunction();
                    }
                });
            })

        @if( $pagecnt == 0 )
            bottomFunction();
        @endif
    })
</script>
@endsection
