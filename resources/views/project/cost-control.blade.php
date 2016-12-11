@extends('layouts.app')

@section('header')
    <h2>{{ $project->name }}</h2>

    <div class="btn-toolbar pull-right">
        <a href="{{ route('project.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop

@section('body')
    <nav class="cost-control-actions">

        <div class="btn-group">
            <a class="btn btn-outline btn-sm btn-info" href="#cost-control-mapping" data-toggle="dropdown">Mapping <span class="caret"></span></a>
        </div>

    </nav>




@stop