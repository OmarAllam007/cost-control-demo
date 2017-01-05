@extends('mail.layout')

@section('body')
    <h3>Activity Mapping Error</h3>

    <p>Eng. {{Auth::user()->name}} tried to upload actual resources at {{date('d/m/Y H:i')}}. Attached activities were not imported because of mapping errors.</p>

    <p>Your action is required to continue in this process.</p>

    <p>Regards</p>
@stop