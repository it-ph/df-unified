<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <?php
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com; font-src https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com");
    ?>
    <head>
        <meta charset="utf-8" />
        <title> DF Unified | @yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- CSRF Token -->
        <meta content="DF Unified" name="description" />
        <meta content="Rico Bugtong" name="author" />

        <!-- CSRF Token -->
        <meta name="_token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
        @yield('css')

        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Fontawesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
        <!-- Icons Css -->
        {{-- <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" /> --}}
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
        <!-- Custom Css-->
        <link href="{{ asset('assets/css/custom.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    </head>

    @yield('body')

    @yield('content')

    @include('layouts.vendor-scripts')

    @yield('custom-js')
    </body>
</html>
