@extends('layouts.master')

@section('title') Change Password @endsection

@section('css')
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('title') Change Password @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    <div class="row to-hide">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <form id="changePasswordForm" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-group row mb-2">
                                    <label for="current_password" class="col-md-2 col-form-label fw-bold">Current Password <strong><span class="important">*</span></strong></label>
                                    <div class="col-md-3">
                                        <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter current password">
                                        <label id="current_passwordError" class="error" style="display:none"></label>
                                    </div>
                                </div>    
                                <div class="form-group row mb-2">
                                    <label for="new_password" class="col-md-2 col-form-label fw-bold">New Password <strong><span class="important">*</span></strong></label>
                                    <div class="col-md-3">
                                        <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter new password">
                                        <label id="new_passwordError" class="error" style="display:none"></label>
                                    </div>
                                </div>    
                                <div class="form-group row mb-2">
                                    <label for="confirm_password" class="col-md-2 col-form-label fw-bold">New Confirm Password <strong><span class="important">*</span></strong></label>
                                    <div class="col-md-3">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter new confirm password">
                                        <label id="confirm_passwordError" class="error" style="display:none"></label>
                                    </div>
                                </div>    
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-md-2 col-form-label fw-bold">&nbsp;</label>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" id="btn_save" class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i> Update Password</button>
                                    </div>
                                </div>
                            </div>    
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>  
@endsection

@section('script')

@endsection

@section('custom-js')
    <script src="{{asset('scripts/change-password.js')}}"></script>
@endsection
