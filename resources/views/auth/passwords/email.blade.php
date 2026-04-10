@extends('layouts.app')

@section('content')
<div class="container mt-5 mt-lg-0">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mt-5 mb-5 login-form">
                <div class="card-header"><h3 class="text-center fw-semibold font30 my-4">{{ __('Reset Password') }}</h3></div>
                <div class="card-body">
                    <div class="cds-form-container">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ url('forgot-password') }}">
                            @csrf
                                                  @if ($errors->has('email'))
    <div class="alert alert-danger">
        {{ $errors->first('email') }}
    </div>
@endif
                            @if (session('message'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <div class="row mb-3">                            
                                <div class="col-xl-12">
                                {!! FormHelper::formInputEmail(['name'=>"email","label"=>"Email address","required"=>true,'events'=>
                                    ['oninput=this.value.replace(/\s+/g, "")']]) !!}  
                                </div>
                            </div>
                            <div class="row justify-content-center mb-0 mt-4">
                                <div class="col-xl-6 col-md-10 text-center">
                                    <button type="submit" class="CdsTYButton-btn-primary">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
