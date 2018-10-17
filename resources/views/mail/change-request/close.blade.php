@extends('mail.layout')

@section('body')
    <p>Dear Eng. {{$user}},</p>

    <p>Eng. {{$change_request->closed_by()->first()->name}} has close the change request in Project -  {{$change_request->project->name}}.</p>

    @php $link = url('/project/budget/' . $change_request->project->id); @endphp
    <p><a href="{{$link}}">{{$link}}</a></p>

    <p>Regards</p>
@endsection