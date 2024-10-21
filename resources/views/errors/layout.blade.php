<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">

        <title>@yield('title')</title>
        <link rel="icon" sizes="16x16" href="{{asset('favicon.ico')}}">

        <!-- Google font -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:500" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Titillium+Web:700,900" rel="stylesheet">

        <!-- Custom Errors Styles -->
        <link href="{{asset('assets/css/errors.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('assets/plugins/bubbles/bubbles.css')}}" />
    </head>
    <body>
        <div id="notfound">
            <ul class="bg-bubbles" style="z-index: -1;">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
            <div class="notfound">
                <div class="notfound-error">
                    <h1>@yield('code')</h1>
                </div>
                @yield('message')
                <a href="{{ route('home') }}">Go To Homepage</a>
            </div>
        </div>
    </body>
</html>
