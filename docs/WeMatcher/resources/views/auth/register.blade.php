@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4 mb-4">
                <div class="card-header wematcher_card_header">{{ __('Register') }}</div>

                <div class="card-body auth_panel">

                    @include('auth.sociallink')

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="email-box">
                            <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('E-Mail Address') }}">
                            <div class="box-hint-icon">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="password-box">
                            <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">
                            <div class="box-hint-icon">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="common-btn primary shadow mt-4">
                            {{ __('Register') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
