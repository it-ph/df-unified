@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Login')
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
                                            <h5 class="text-white">Welcome Back !</h5>
                                            <p>Sign in to access DF Unified.</p>
                                        </div>
                                    </div>
                                    <div class="col-3 align-self-end pb-3">
                                        <img src="{{ URL::asset('/assets/images/authentication.png') }}" class="img-fluid" width="80%">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-3">
                                <form id="signInForm" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input name="email" type="text"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" id="email"
                                            placeholder="Enter your email address" autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label">Password</label>
                                        <div
                                            class="input-group auth-pass-inputgroup @error('password') is-invalid @enderror">
                                            <input type="password" name="password"
                                                class="form-control  @error('password') is-invalid @enderror"
                                                id="userpassword" value="" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-label="Password" aria-describedby="password-addon">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="float-end">
                                        <a href="{{ url('forgot-password') }}">Forgot
                                            Password?</a>
                                    </div>
                                    <br>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary btn-block w-100 text-uppercase" type="submit" id="btn_submit"> SIGN IN</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <div>Copyright © {{ date('Y') }}</script>
                                eClerx Philippines, Inc. | DF Unified v1.0.0
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

