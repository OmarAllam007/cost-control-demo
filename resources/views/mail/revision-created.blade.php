@extends('mail.layout')

@section('body')
    <p>Dear Engineers,</p>

    <p>A new revisions, <strong>{{$revision->name}}</strong> has been created on <strong>{{$project->name}}</strong> project.</p>

    <p>You can view revision summary here <a href="{{$revision->url()}}">{{$revision->url()}}</a></p>

    <p>Regards</p>
@endsection