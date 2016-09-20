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
        <li><a href="#wbs-structure" data-toggle="tab">WBS</a></li>
        <li><a href="#quantity-survey" data-toggle="tab">Quantity Survey</a></li>
        <li class="active"><a href="#breakdown" data-toggle="tab">Breakdown</a></li>
        <li><a href="#resources" data-toggle="tab">Resources</a></li>
        <li><a href="#productivity" data-toggle="tab">Productivity</a></li>
        <li><a href="#boq" data-toggle="tab">BOQs</a></li>
    </ul>

    <div class="tab-content">
        <section class="tab-pane" id="wbs-structure">
            @include('project.tabs._wbs')
        </section>

        <section class="tab-pane" id="quantity-survey">
            @include('project.tabs._quantity-survey')
        </section>

        <section class="tab-pane active" id="breakdown">
            @include('project.tabs._breakdown')
        </section>

        <section class="tab-pane" id="resources">
            @include('project.tabs._resources')
        </section>

        <section class="tab-pane" id="productivity">
            @include('project.tabs._productivity')
        </section>
        <section class="tab-pane" id="boq">
            @include('project.tabs._boq')
        </section>
    </div>
@stop
