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
        <li class="active"><a href="#breakdown" data-toggle="tab">Breakdown</a></li>
        <li><a href="#resources" data-toggle="tab">Resources</a></li>
        <li><a href="#productivity" data-toggle="tab">Productivity</a></li>
    </ul>

    <div class="tab-content">
        <section class="tab-pane" id="wbs-structure">
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

        <section class="tab-pane active" id="breakdown">
            <div class="form-group tab-actions clearfix">
                <a href="{{route('breakdown.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i> Add Breakdown
                </a>
            </div>

            @if ($project->breakdown_resources)
                <div class="scrollpane">
                <table class="table table-condensed table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>WBS</th>
                        <th>Activity</th>
                        <th>Breakdown</th>
                        <th>Cost Account</th>
                        <th>Eng. Qty.</th>
                        <th>Budget Qty.</th>
                        <th>Resource Qty.</th>
                        <th>Resource Waste</th>
                        <th>Resource Type</th>
                        <th>Resource Code</th>
                        <th>Resource Name</th>
                        <th>Price/Unit</th>
                        <th>Unit of measure</th>
                        <th>Budget Unit</th>
                        <th>Budget Cost</th>
                        <th>No. Of Labors</th>
                        <th>Productivity (Unit/Day)</th>
                        <th>Productivity Ref</th>
                        <th>Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($project->breakdown_resources as $resource)
                        <tr>
                            <td>{{$resource->breakdown->wbs_level->path}}</td>
                            <td>{{$resource->breakdown->std_activity->name}}</td>
                            <td>{{$resource->breakdown->template->name}}</td>
                            <td>{{$resource->breakdown->cost_account}}</td>
                            <td>{{$resource->eng_qty}}</td>
                            <td>{{$resource->budget_qty}}</td>
                            <td>{{$resource->resource_qty}}</td>
                            <td>{{$resource->resource_waste}}</td>
                            <td>{{$resource->resource->resource->types->name}}</td>
                            <td>{{$resource->resource->resource->resource_code}}</td>
                            <td>{{$resource->resource->resource->name}}</td>
                            <td>{{$resource->resource->resource->rate}}</td>
                            <td>{{$resource->resource->resource->units->type}}</td>
                            <td>0</td>
                            <td>0</td>
                            <td>{{$resource->labor_count}}</td>
                            <td>{{$resource->productivity->after_reduction}}</td>
                            <td>{{$resource->productivity->description}}</td>
                            <td>{{$resource->remarks}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="alert alert-warning"><i class="fa fa-info-circle"></i> No breakdowns added</div>
            @endif
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
