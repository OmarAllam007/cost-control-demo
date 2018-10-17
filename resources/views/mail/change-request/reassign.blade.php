@extends('mail.layout')

@section('body')
    <p>Dear Eng. {{$change_request->assigned_to()->first()->name}},</p>

    <p>Change Request #{{$change_request->id}} has been assigned to you in
        project {{$change_request->project->name}}.</p>

    @php $link = url('/project/budget/' . $change_request->project->id); @endphp
    <p><a href="{{$link}}">{{$link}}</a></p>

    <p>Regards</p>
@endsection