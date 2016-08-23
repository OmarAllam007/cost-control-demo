@extends('layouts.app')

@section('header')
    <h2 class="panel-title">Project - {{$project->name}}</h2>

    <form action="{{ route('project.destroy', $project)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('project.edit', $project)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>
            Edit</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('project.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    @if (trim($project->description))
        <div class="panel panel-default">
            <div class="panel-body">
                {!! nl2br(e($project->description)) !!}
            </div>
        </div>
    @endif

    <ul class="nav nav-tabs">
        <li class="active"><a href="#wbs-structure" data-toggle="tab">WBS</a></li>
        <li><a href="#breakdown" data-toggle="tab">Breakdown</a></li>
        <li><a href="#resources" data-toggle="tab">Resources</a></li>
        <li><a href="#productivity" data-toggle="tab">Productivity</a></li>
    </ul>

    <div class="tab-content">
        <section class="tab-pane active" id="wbs-structure">
            <div class="form-group tab-actions clearfix">
                <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i> Add Level
                </a>
            </div>

            @if ($project->wbs_tree)
                <ul class="list-unstyled tree">
                    @foreach($project->wbs_tree as $wbs_level)
                        @include('wbs-level._recursive', compact('wbs_level'))
                    @endforeach
                </ul>
            @else
                <div class="alert alert-info"><i class="fa fa-info"></i> No WBS found</div>
            @endif
        </section>

        <section class="tab-pane" id="breakdown">
            <div class="form-group tab-actions clearfix">
                <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i> Add Breakdown
                </a>
            </div>
        </section>
        <section class="tab-pane" id="resources">
            <div class="form-group tab-actions clearfix">
                <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i> Add Resource
                </a>
            </div>
        </section>
        <section class="tab-pane" id="productivity">
            <div class="form-group tab-actions clearfix">
                <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i> Add Productivity
                </a>
            </div>
        </section>
    </div>
@stop
