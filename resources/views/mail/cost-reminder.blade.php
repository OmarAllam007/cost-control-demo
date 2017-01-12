@extends('mail.layout')

@section('body')
    <p>Dear Eng. {{$user->name}},</p>

    <p>This is a reminder to upload actual cost for project {{$project->name}} on period {{$period->name}}</p>

    <p>Please upload data for interval starting from <strong>{{$startDate->format('d/m/Y')}}</strong> to <strong>{{$endDate->format('d/m/Y')}}</strong></p>

    <p>Regards</p>
@endsection