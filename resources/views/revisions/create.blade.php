@extends('layouts.' . (request('iframe')? 'iframe':'app'))

@section('header')
    <h4>Create revision &mdash; {{$project->name}}</h4>
@endsection

@section('body')
    <form action="{{route('revisions.store', $project) . (request('iframe')? '?iframe=1' : '')}}" method="post">
        {{csrf_field()}}

        @include('revisions._form')
    </form>
@endsection