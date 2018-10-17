@extends('mail.layout')

@section('body')
    <p>Dear Eng. {{$change_request->assigned_to()->first()->name}},</p>

    <p>Eng. {{$change_request->created_by()->first()->name}} has create a new change request into {{$change_request->project->name}}.</p>

    @php $link = url('/project/budget/' . $change_request->project->id); @endphp
    <p><a href="{{$link}}">{{$link}}</a></p>

    <p>Regards</p>
@endsection