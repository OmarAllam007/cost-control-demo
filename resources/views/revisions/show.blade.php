@extends('layouts.app')

@section('header')
    <h3>{{$project->name}} &mdash; {{$revision->name}}</h3>

    <div class="pull-right">
        <a href="{{$revision->url()}}/export" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
        <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')

@endsection