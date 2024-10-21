@extends('errors.layout')
@section('title', __('403 Forbidden'))
@section('code', '403')
@section('message')
    <h2>Oops! you're FORBIDDEN to access this page.</h2>
    <p>If you think this is an error. please contact your system administrator.</p>
@endsection