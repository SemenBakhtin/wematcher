@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8 text-center">
            <p class="regpanelheader mb-5">{{ __('Edit Your Profile') }}</p>
            <form method="POST" action="{{ route('profile.edit') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $person->id }}">
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{ __('Avatar') }}: *</label>
        
                    <div class="col-md-6">
                        <div class="avatareditor" data-lang="{{ app()->getLocale() }}" data-src="{{ $person->avatar }}"></div>
                        @error('avatar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}: *</label>
        
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $person->name }}" required autocomplete="name" autofocus>
        
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
        
                <div class="form-group row">
                    <label for="country" class="col-md-4 col-form-label text-md-right">{{ __('Country') }}: *</label>
        
                    <div class="col-md-6">
                        <div class="form-control p-0" style="box-sizing: content-box">
                            <select id="country" class="w-100 selectpicker @error('country') is-invalid @enderror" name="country" value="{{ $person->country }}">
                                <option value="" hidden>------</option>
            
                                @foreach( App\Constants\Constants::$COUNTRY as $key => $value )
                                    <option value="{{$key}}" data-content="<span class='flag-icon flag-icon-{{ strtolower($key) }}'></span>     {{$value}}" @if($key==$person->country) selected @endif></option>;
                                @endforeach
                            </select>
                        </div>
                        @error('country')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label for="birthday" class="col-md-4 col-form-label text-md-right">{{ __('Birthday') }}: *</label>

                    <div class="col-md-6">
                        <input required id="birthday" name="birthday" class="form-control @error('birthday') is-invalid @enderror"
                            value="{{ $person->birthday }}" placeholder="08/08/2000"/>

                        @error('birthday')
                            <span class="invalid-feedback" role="alert" style="display:block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div> --}}

                <div class="form-group row">
                    <label for="age" class="col-md-4 col-form-label text-md-right">{{ __('Age') }}: *</label>

                    <div class="col-md-6">
                        <select id="age" class="form-control @error('age') is-invalid @enderror" name="age" value="{{ $person->age }}">
                            <option value="" hidden>------</option>
        
                            @foreach( App\Models\Person::getEnum('age') as $value)
                                <option value="{{$value}}" @if($value==$person->age) selected @endif>{{$value}}</option>
                            @endforeach
                        </select>

                        @error('gender')
                            <span class="invalid-feedback" role="alert" style="display:block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="gender" class="col-md-4 col-form-label text-md-right">{{ __('Gender') }}: *</label>

                    <div class="col-md-6">
                        <select id="gender" class="form-control @error('gender') is-invalid @enderror" name="gender" value="{{ $person->gender }}">
                            <option value="" hidden>------</option>
        
                            @foreach( App\Models\Person::getEnum('gender') as $value)
                                <option value="{{$value}}" @if($value==$person->gender) selected @endif>{{$value}}</option>
                            @endforeach
                        </select>

                        @error('gender')
                            <span class="invalid-feedback" role="alert" style="display:block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
        
                <div class="form-group row mt-5">
                    <div class="col-md-12" style="text-align:center;">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    $('#birthday').datepicker({
        uiLibrary: 'bootstrap4'
    });
    $(".selectpicker").selectpicker();
});
</script>
@endsection
