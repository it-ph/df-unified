@extends('errors.layout')
@section('title', __('419 Page Expired'))
@section('code', '419')
@section('message')
    <h2>Oops! page has expired.</h2>
    <p>Sorry, your session has expired. Please refresh the page and try again.</p>
@endsection