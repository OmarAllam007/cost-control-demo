@extends('mail.layout')

@section('body')
    <p>Dear {{$user->name}},</p>
    <p></p>

    <p>Please find attached the report for {{$project->name}}</p>

    <p>&nbsp;</p>
    <p>&nbsp;</p>

    <p>Regards</p>
@endsection