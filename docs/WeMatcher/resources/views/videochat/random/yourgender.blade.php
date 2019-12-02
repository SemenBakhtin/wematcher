@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12 text-center">
            <h1>
                {{ __('Interested in') }}
            </h1>
        </div>
    </div>
    <div class="row justify-content-center mt-4 gender-wrapper">
        <div class="col-md-3 col-sm-6 text-center">
            <a href="{{route('videochat.random.yourgenderupdate', ['Male'])}}">
                <div class="male">
                    <div class="picture"></div>
                    <div class="name">{{ __('Male') }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <a href="{{route('videochat.random.yourgenderupdate', ['Female'])}}">
                <div class="female">
                    <div class="picture"></div>
                    <div class="name">{{ __('Female') }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <a href="{{route('videochat.random.yourgenderupdate', ['Couple'])}}">
                <div class="couple">
                    <div class="picture"></div>
                    <div class="name">{{ __('Couple') }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <a href="{{route('videochat.random.yourgenderupdate', ['Trans'])}}">
                <div class="trans">
                    <div class="picture"></div>
                    <div class="name">{{ __('Trans') }}</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
