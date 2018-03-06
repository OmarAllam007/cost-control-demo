@extends('mail.layout')

@section('body')
    <p>Dear {{$user->user->name}},</p>
    <p></p>

    <p>Please find attached budget reports for {{$project->name}}</p>

    <p>&nbsp;</p>

    <p>Regards</p>
@endsection