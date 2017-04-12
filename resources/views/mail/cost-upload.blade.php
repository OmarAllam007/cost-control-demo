@extends('mail.layout')

@section('body')
    <p>Dear Eng. {{$owner->name}},</p>

    <p>Eng. {{$batch->user->name}} has uploaded a new file into {{$batch->project->name}}.</p>

    <p>Please find attached the uploaded file. You can find upload details on the following link:</p>

    @php $link = url('/actual-batches/' . $batch->id); @endphp
    <p><a href="{{$link}}">{{$link}}</a></p>

    <p>Regards</p>
@endsection