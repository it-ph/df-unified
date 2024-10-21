@extends('errors.layout')
@section('title', __('401 Unauthorized'))
@section('code', '401')
@section('message')
    <h2>Oops! you're not AUTHORIZED to access this page.</h2>
    <p>If you think this is an error. please contact your system administrator.</p>
@endsection