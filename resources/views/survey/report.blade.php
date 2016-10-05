@extends('layouts.app')
@section('header')
    <h1>Project - {{$project->name}}</h1>
    <a href="{{URL::previous()}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')
    @if ($project->wbs_tree->count())
        <ul class="list-unstyled tree">
            @foreach($project->wbs_tree as $wbs_level)
                @include('survey._recursive_report', compact('wbs_level'))
            @endforeach
        </ul>
    @endif
@endsection