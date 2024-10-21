<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{ asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{ asset('assets/libs/node-waves/node-waves.min.js')}}"></script>

@yield('script')

<!-- App js -->
<script src="{{ asset('assets/js/app.min.js')}}"></script>

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- Sweet alert init js-->
<script src="{{ asset('assets/js/pages/sweet-alerts-custom.init.js') }}"></script>

<!-- Axios Js -->
<script src="{{asset('dist/js/axios.min.js')}}"></script>

<!-- Toastr Js -->
<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>

<!-- Moment Js -->
<script src="{{ asset('assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/libs/moment/moment-timezone.min.js') }}"></script>
@yield('script-bottom')
