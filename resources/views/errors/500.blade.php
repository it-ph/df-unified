@extends('errors.layout')
@section('title', __('500 Server Error'))
@section('code', '500')
@section('message')
    <h2>Oops! looks like something went wrong.</h2>
    <p>I don't know what happened but it's my fault. Please contact your system administrator.</p>
@endsection