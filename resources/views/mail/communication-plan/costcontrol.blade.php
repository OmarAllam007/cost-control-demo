@extends('mail.layout')

@section('body')
    <p>Dear {{$user->user->name}},</p>

    <p></p>

    <p>Please find attached cost control reports for {{$project->name}} &mdash; {{$period->name}}</p>

    <p>&nbsp;</p>

    <p>Regards</p>
@endsection