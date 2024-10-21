@yield('css')

<!-- Bootstrap Css -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Fontawesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
<!-- App Css-->
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- Custom Css-->
<link href="{{ asset('assets/css/custom.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Toastr Css -->
<link href="{{ asset('assets/libs/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #00599D;
        color: #fff;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #00599D;
        color: #fff;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: #00599D;
        color: #fff;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #00599D !important;
        color: #fff !important;
    }

    input[type="checkbox"] {
        accent-color: #00599D;
    }
</style>
