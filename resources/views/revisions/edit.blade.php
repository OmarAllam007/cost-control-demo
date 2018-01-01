@extends('layouts.app')

@section('header')
    <h4>Update revision &mdash; {{$project->name}}</h4>
@endsection

@section('body')
    <form action="{{route('revisions.update', [$project, $revision])}}" method="post">
        {{csrf_field()}} {{method_field('put')}}

        @include('revisions._form')
    </form>
@endsection