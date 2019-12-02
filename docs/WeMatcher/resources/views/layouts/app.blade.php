<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <title>{{ config('app.name', 'WeMatcher') }}</title>

    <meta name="google-site-verification" content="ysq28cBU5hTfaodmGw5BKWgtzJxzPVKgjBStpxD8BmQ">
    <!-- Fonts -->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/flag-icon.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-select.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="app" id="wematcher_app">
        <div class="main_content">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" id="app_navbar">
                <div class="container">
                    <a href="{{ url('/') }}">
                        <div class="navbar-brand_">
                            <img src="{{ asset('img/logo.jpg') }}" alt="Avatar">
                        </div>
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('videochat/random*') ? 'active' : '' }}" href="{{ route('videochat.random.index') }}">{{ __('Random Chat') }}</a>
                            </li>
                        </ul>
						<ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('videochat/dating*') ? 'active' : '' }}" href="{{ route('videochat.dating.index') }}">{{ __('Dating') }}</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="localeDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <span class='flag-icon flag-icon-{{ App\Constants\Constants::$LOCALE[app()->getLocale()][1] }}'>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="localeDropdown">
                                    @foreach( App\Constants\Constants::$LOCALE as $key => $value )
                                        <a class="dropdown-item" href="{{ route('locale', [$key]) }}">
                                            <span class='flag-icon flag-icon-{{ strtolower($value[1]) }}'></span> {{ $value[0] }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main>
                <div class="maincontent">
                    @yield('content')
                    @auth
                        <div id="videocall" data-userid="{{ auth()->user()->id }}"></div>
                    @endauth
                    <div
                        id="wematcher_notification"
                        data-lang="{{ app()->getLocale() }}"
                        @auth
                            data-userid="{{ auth()->user()->id }}"
                        @endauth
                        >
                    </div>
                </div>
                <div class="footer shadow-sm bg-light">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <ul class="list-unstyled quick-links flex-row justify-content-center mb-0">
                                <li><a href="{{ url('/termsofservice') }}">{{ __('Terms & Conditions') }}</a></li>
                                <li><a href="{{ url('/privacypolicy') }}">{{ __('Privacy Policy') }}</a></li>
                                <li><a href="{{ url('/cookiepolicy') }}">{{ __('Cookie Policy') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="left_sidebar bg-white shadow-sm">
            @if(!Auth::check() || Auth::user()->person->status == 'none')
                <div class="user_letter_avatar none">
                    <i class="fas fa-user"></i>
                </div>
            @else
                <img src="{{ Auth::user()->person->avatar }}" class="user_avatar">
            @endif
            @auth
                {{ Auth::user()->person->name }}
            @endauth
            <ul class="left-menu mt-4">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('login*') ? 'active' : '' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('register*') ? 'active' : '' }}" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('profile*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
                            {{ __('My profile') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('friend/invites*') ? 'active' : '' }}" href="{{ route('friend.invites') }}">
                            {{ __('Notification') }}
                            @if(isset($invites) && count($invites)>0) ({{ count($invites) }}) @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </ul>
        </div>
        <div class="right_sidebar bg-white shadow-sm">
            @include('layouts.rightsidebar')
        </div>

        <div class="toggle_btn left">
            >
        </div>

        <div class="toggle_btn right">
            <
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('script')
    @stack('scripts')
    @auth
    <script>
        function call(userid, name, avatar_url, call_url, end_url, accept_url, reject_url){
            videocallComponent.setStatus('call', userid, name, avatar_url, call_url, end_url, accept_url, reject_url);
        };

        function invite(toemail){
            $.ajax({
                url: "{{ route('friend.addrequest') }}",
                data: {'friend': toemail},
                success:  function(res) {
                    if (res.success) {
                        notificationComponent.notify("{{ __("Wonderful!") }}", "{{ __("Invitation succesfully sent") }}", "success");
                    }
                }
            })
        }
    </script>
    @endauth
    <script>
        $(document).ready( function() {
            $('.toggle_btn.left').click(function() {
                $('.left_sidebar').toggle(100);
                $(this).text($(this).text().trim()=='>'?'<':'>');
            })

            $('.toggle_btn.right').click(function() {
                $('.right_sidebar').toggle(100);
                $(this).text($(this).text().trim()=='>'?'<':'>');
            })


            $('#wematcher_app').resize(function() {
                window.top.postMessage({'type':'height', 'value': $('#wematcher_app').height()}, '*');
            })

            function changeSize(){
                console.log("changed");
                window.top.postMessage({'type':'height', 'value': $('#wematcher_app').height()}, '*');
            }

            new ResizeSensor($('#wematcher_app'), function(){
                changeSize();
            });

            changeSize();
        })
    </script>
</body>
</html>
