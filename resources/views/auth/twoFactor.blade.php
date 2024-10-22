@extends('layouts.master-without-nav')

@section('title')
    Verify
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('assets/plugins/login/css/page-auth.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/plugins/login/css/firefly.css')}}" />
@endsection

@section('body')

    <body>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
    @endsection

    @section('content')
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary-head">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="text-white p-4">
                                            <h5 class="text-white">DF Unified</h5>
                                            <p>Two-Factor Verification</p>
                                        </div>
                                    </div>
                                    <div class="col-3 align-self-end pb-3">
                                        <img src="{{ URL::asset('/assets/images/authentication.png') }}" class="img-fluid" width="80%">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-3">
                                <div class="row">
                                    <p class="text-muted">
                                        <?php
                                            function hide_email($email)
                                            {
                                                if(filter_var($email, FILTER_VALIDATE_EMAIL))
                                                {
                                                    list($first, $last) = explode('@', $email);
                                                    $first = str_replace(substr($first, '3'), str_repeat('*', strlen($first)-3), $first);
                                                    $last = explode('.', $last);
                                                    $last_domain = str_replace(substr($last['0'], '0'), str_repeat('*', strlen($last['0'])-0), $last['0']);
                                                    $hide_email = $first.'@'.$last_domain.'.'.$last['1'];
                                                    return $hide_email;
                                                }
                                            }
                                            $email = Auth::user()->email;
                                            $email =  hide_email($email);
                                        ?>

                                        We've sent a verification code to your email {{ $email }}.
                                        If you haven't received it, just click <a href="{{ route('verify.resend') }}">here</a>.
                                    </p>
                                </div>
                                <div class="row">
                                    <form id="verify_form" method="POST" action="{{ route('verify.store') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <input name="two_factor_code" type="text" class="login-form-txt form-control{{ $errors->has('two_factor_code') ? ' is-invalid' : '' }}" required autofocus placeholder="Two Factor Code">
                                            @if($errors->has('two_factor_code'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('two_factor_code') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary btn-block w-100 text-uppercase" type="submit" id="btn_submit"> VERIFY</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <div>Copyright © {{ date('Y') }}
                                eClerx Philippines | DF Unified v1.0.0
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

    @endsection

    @section('custom-js')
        <script src="{{asset('scripts/auth.js')}}"></script>
    @endsection
