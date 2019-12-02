<div class="text-center mb-4">
    <div data-url="{{ url('/auth/redirect/facebook') }}" class="social-btn facebook" data-social="facebook">
        <i class="fab fa-facebook" aria-hidden="true"></i>
    </div>
    <div data-url="{{ url('/auth/redirect/google') }}" class="social-btn google" data-social="google">
        <img src="{{ asset('img/google.png') }}">
    </div>
    <div data-url="{{ url('/auth/redirect/vkontakte') }}" class="social-btn vk" data-social="vk">
        <i class="fab fa-vk" aria-hidden="true"></i>
    </div>
    <div data-url="{{ url('/auth/redirect/weixin') }}" class="social-btn weixin" data-social="weixin">
        <i class="fab fa-weixin" aria-hidden="true"></i>
    </div>
</div>

@push('scripts')
    <script>

        $(document).ready( function() {

            function CheckLoginStatus() {
                if (signinWin.closed) {
                    if(signinWin.signed == 1){
                        window.location = "{{ url('/') }}";
                    }
                }
                else setTimeout(CheckLoginStatus, 100);
            }

            function screenCenterPos(w, h){
                var left = (screen.width/2)-(w/2);
                var top = (screen.height/2)-(h/2);
                return {'x': left, 'y': top};
            }

            var sizes = {
                'google': {'width': 440, 'height': 510},
                'vk': {'width': 655, 'height': 350},
                'facebook': {'width': 410, 'height': 360},
                'weixin': {'width': 210, 'height': 360}
            };

            $('.social-btn').click(function() {
                var size = sizes[$(this).data('social')];
                var pos = screenCenterPos(600, 600);
                $.cookie('socialcallback', 0);
                signinWin = window.open($(this).data('url'), "Login", "width="+size.width+",height="+size.height+",toolbar=0,scrollbars=0,status=0,resizable=0,location=0,menuBar=0,left=" + pos.x + ",top=" + pos.y);
                signinWin.signed = 0;
                CheckLoginStatus();
                signinWin.focus();
                return false;
            })
        })
    </script>
@endpush
