@extends('layouts.master')

@section('title') Settings @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Allowed Date to Edit @endslot
        @slot('title') Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="updateAllowedDateRangeForm" action="{{ route('settings.update', $allowed_daterange) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <label class="mt-1"><strong>Allowed Date Range to Edit</strong> <span style="font-weight: bold; color: red">*</span></label>
                        <input type="hidden" name="date_from" id="date_from" value="{{ $date_from }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ $date_to }}">
                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                            <input type="text" class="form-control" name="allowed_date_range" id="allowed_date_range" style="display: none">
                        </div>
                        <button type="button" onclick="update('updateAllowedDateRangeForm')" data-toggle="tooltip" title="Click to update and set Allowed Date Range to Edit" class="mt-3 btn btn-primary float-end"> <strong> <i class="fa fa-save"></i>  UPDATE </strong></button>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript">
        $(function() {
            var start = moment($('#date_from').val());
            var end = moment($('#date_to').val());
        
            function cb(start, end) {
                $('#reportrange span').html(start.format('dddd, MMMM D, YYYY hh:mm a') + ' - ' + end.format('dddd, MMMM D, YYYY hh:mm a'));
                $('#allowed_date_range').val(start.format('YYYY-MM-DD hh:mm a') + ' - ' + end.format('YYYY-MM-DD hh:mm a'));
            }
        
            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                // "autoApply": true,
                "timePicker": true,
                "applyButtonClasses": "btn-primary",
                "cancelClass": "btn-danger"
            }, cb);
        
            cb(start, end);
        });
    </script>
@endsection
