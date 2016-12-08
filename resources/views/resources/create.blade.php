@extends('layouts.app')

@section('header')
    <h2>
        Add Resources
        @if ($project_id = request('project'))
            &mdash; {{App\Project::find($project_id)->name}}
        @endif
    </h2>

    @if ($project_id)
        <a href="{{ route('project.show', $project_id) }}#ResourcesArea" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
    @else
        <a href="{{ route('resources.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
    @endif
@stop

@section('body')
    {{ Form::open(['route' => ['resources.store','project'=>request('project')]]) }}

        @include('resources._form', ['override' => false])

    {{ Form::close() }}
@stop
