@extends('layouts.master')

@section('title') Reallocate Task @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Task @endslot
        @slot('title') Reallocate Task @endslot
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
                    <table id="tbl_jobs" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                <th class="text-center">Account No</th>
                                <th class="text-center">Account Name</th>
                                @if(in_array('admin',$user['roles']))<th>Client Name</th>@endif
                                <th class="text-center">Type of Request</th>
                                <th class="text-center">Num Pages</th>
                                <th class="text-center">Supervisor</th>
                                <th class="text-center">Supervisor</th>
                                <th class="text-center">Created On</th>
                                <th class="text-center">Agreed SLA</th>
                                <th class="text-center">SLA Missed</th>
                                <th class="text-center">Potential SLA Miss</th>
                                <th class="text-center">Designer</th>
                                <th class="text-center">Designer</th>
                                <th class="text-center">Status</th>
                                <th width="5%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('pages.admin.reallocation.job.modal')
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.js') }}"></script>
    <script>
        window.userRoles = @json($user['roles']);
    </script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/reallocatejob.js')}}"></script>
@endsection
