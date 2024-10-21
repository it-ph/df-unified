<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>DF Unified | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="DF Unified" name="description" />
    <meta content="Rico Bugtong" name="author" />

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="noindex">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
    @include('layouts.head-css')
</head>

@section('body')
    <body data-sidebar="dark" data-keep-enlarged="true" class="vertical-collpsed">
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('layouts.right-sidebar')
    <!-- /Right-bar -->

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')

    <!-- Base Url -->
    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/')) !!}
    </script>

    @yield('custom-js')

    {{-- <script>
        history.scrollRestoration = "manual";
        $(window).scrollTop(0);
    </script> --}}

    <script>
        toastr.options = {
            "positionClass": "toast-bottom-right",
        }

        $('#log-out').click(function(){
            Swal.fire({
                title: 'Sign Out?',
                text: "Are you sure you want to sign-out?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                confirmButtonClass: 'btn btn-primary mt-2',
                cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                buttonsStyling: false,
                allowOutsideClick: false
            }).then(function(result) {
                if (result.value) {
                    Swal.fire({
                        title: 'Thank you!',
                        icon: 'success',
                        allowOutsideClick: false
                    });
                    window.location.href = "{{ URL::to('logout') }}"
                }
            });
        });
    </script>
</body>

</html>
