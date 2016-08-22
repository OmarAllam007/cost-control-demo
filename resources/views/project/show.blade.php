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

    <section class="children">
        <h3 class="page-header">WBS</h3>

        <div class="form-group clearfix">
            <a href="{{route('wbs-level.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>
                Add Level</a>
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
@stop
