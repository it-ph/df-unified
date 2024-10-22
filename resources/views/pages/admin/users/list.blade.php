@extends('layouts.master')

@section('title') Users @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb_w_button')
        @slot('li_1') Manage / Users @endslot
        @slot('title') Users
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="USER.showModal()"><i class="fas fa-plus"></i> Create</button>
            @if(in_array('admin',$user['roles']) || in_array('manager',$user['roles']))
                <button type="button" id="btn_export" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fas fa-download"></i> Template</button>
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="USER.showUploadModal()"><i class="fas fa-upload"></i> Upload</button>
            @endif
        @endslot
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
                    <table id="tbl_users" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Full Name</th>
                                <th>Email Address</th>
                                @if(in_array('admin',$user['roles']))<th>Client</th>@endif
                                <th>Supervisor</th>
                                <th>Supervisor</th>
                                <th>Role</th>
                                <th>Last Login Time</th>
                                <th class="text-center">Status</th>
                                <th width="5%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('pages.admin.users.user-modal')
    @include('pages.admin.users.upload-modal')
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
    <script src="{{asset('scripts/users.js')}}"></script>
@endsection


