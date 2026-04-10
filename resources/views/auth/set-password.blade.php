@extends('layouts.app')

@section('content')
<div class="container mt-5 mt-lg-0">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mt-5 mb-5 login-form">
                <div class="card-header"><h3 class="text-center fw-semibold font30 my-4">{{ __('Reset Password') }}</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ url('set-password/'.$user->unique_id) }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-lg-end">{{ __('Email Address') }}<span class="text-danger">*</span></label>
                            <div class="col-xl-7 col-md-12 col-lg-7">
                                <div class="form-control-lg form-control">{{ $user->email }}</div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-lg-end">{{ __('Password') }}<span class="text-danger">*</span></label>
                            <div class="col-xl-7 col-md-12 col-lg-7">
                            <div class="wrap-input">
                            <button type="button" class="btn-show-pass ico-20">
                                <span class="flaticon-visibility eye-pass"></span>
                            </button>
                                <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password"  placeholder="Enter New Password">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror                               
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-lg-end">{{ __('Confirm Password') }}<span class="text-danger">*</span></label>
                            <div class="col-xl-7 col-md-12 col-lg-7">
                            <div class="wrap-input">
                            <button type="button" class="btn-show-pass ico-20">
                                <span class="flaticon-visibility eye-pass"></span>
                            </button>
                                <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password"  placeholder="Enter Confirm Password">
                                @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror      
                            </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-0 mt-4">
                            <div class="col-xl-12">
                                <button type="submit" class="CdsTYButton-btn-primary m-auto">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("javascript")

@endsection