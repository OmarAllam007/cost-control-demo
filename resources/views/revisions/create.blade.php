@extends('layouts.app')

@section('header')
    <h4>Create revision &mdash; {{$project->name}}</h4>
@endsection

@section('body')
    <form action="{{route('revisions.create', $project)}}" method="post">
        {{csrf_field()}}

        @include('revisions._form')
    </form>
@endsection