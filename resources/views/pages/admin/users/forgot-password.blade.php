@extends('layouts.master-without-nav')

@section('title')
    Forgot Password
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
                                            <p>Forgot Password</p>
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
                                        Please enter your email address then click send.
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-top: 5px">
                                        @include('notifications.success')
                                        @include('notifications.error')
                                    </div>
                                </div>
                                <div class="row">
                                    <form id="reset_form" method="POST" action="{{route('forgot.password.submit')}}">
                                        @csrf
                                        <div class="mb-3">
                                            <input name="email" type="text" class="login-form-txt form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required autofocus placeholder="Email Address">
                                            @if($errors->has('email'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('email') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary btn-block w-100 text-uppercase" type="submit" id="btn_submit"> SEND</button>
                                        </div>
                                    </form>
                                    <form action="{{route('login')}}" method="GET">
                                        @csrf
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary btn-block w-100 text-uppercase" type="submit">BACK TO SIGN-IN</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <div>Copyright © <script nonce="{{ csp_nonce() }}">document.write(new Date().getFullYear())</script>
                                eClerx Philippines, Inc. | DF Unified v1.0.0
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

    @endsection

    @section('script')
        <script>
            $('#reset_form').on('submit', function(e) {
                e.preventDefault();
                $('#btn_submit').empty();
                $('#btn_submit').append('<i class="fa fa-spinner fa-spin"></i> SENDING...');
                $('#btn_submit').prop("disabled", true);
                this.submit();
            });
        </script>
    @endsection
