@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8 text-center">
            <p class="regpanelheader mb-5">
                {{ __('My Profile') }}
                <a href="{{route('profile.edit')}}"><i class="fas fa-pencil-alt"></i></a>
            </p>
            <div class="form-group row">
                <div class="col-md-4 text-md-right">{{ __('Avatar') }}: </div>

                <div class="col-md-6 text-left">
                    <img src="{{ $person->avatar }}">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4 text-md-right">{{ __('Name') }}: </div>

                <div class="col-md-6 text-left">
                    {{ $person->name }}
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-4 text-md-right">{{ __('Country') }}: </div>

                <div class="col-md-6 text-left">
                    <span class='flag-icon flag-icon-{{ strtolower($person->country) }}'></span> {{ App\Constants\Constants::$COUNTRY[$person->country] }}
                </div>
            </div>

            {{-- <div class="form-group row">
                <div class="col-md-4 text-md-right">{{ __('Birthday') }}: *</div>

                <div class="col-md-6">
                    {{ $person->birthday }}
                </div>
            </div> --}}

            <div class="form-group row">
                <div class="col-md-4 text-md-right">{{ __('Age') }}: </div>

                <div class="col-md-6 text-left">
                    {{ $person->age }}
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-4 text-md-right">{{ __('Gender') }}: </div>

                <div class="col-md-6 text-left">
                    {{ $person->gender }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
