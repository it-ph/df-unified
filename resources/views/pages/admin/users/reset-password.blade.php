@extends('layouts.master')

@section('title') 
    Password Reset 
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <a class="btn btn-primary" href="{{url('/')}}" style="text-decoration: none" title="back to login page"><i class='fas fa-chevron-left'></i> Back to Login Page </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4>You have successfully reset your password!</h4>
                    <p>Your new Password is <span style="font-weight: bold">{{ $password }}</span></p>
                </div>
            </div>
        </div>
    </div>

@endsection
