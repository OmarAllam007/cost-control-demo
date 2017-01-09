@extends('layouts.app')

@section('header')
    <h2>Breakdown template - {{$breakdown_template->name}}</h2>
    @if(request('project_id'))
    @else

        <form action="{{ route('breakdown-template.destroy', $breakdown_template)}}" class="pull-right" method="post">

            @can('write', 'breakdown-template')
                <a href="{{ route('breakdown-template.create', ['activity' => $breakdown_template->std_activity_id])}}"
                   class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Add template
                </a>
                <a href="{{ route('breakdown-template.edit', $breakdown_template)}}" class="btn btn-sm btn-primary">
                    <i class="fa fa-edit"></i> Edit
                </a>
            @endcan
            @can('write', 'breakdown-template')
                {{csrf_field()}} {{method_field('delete')}}
                <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
            <a href="{{ route('std-activity.show', $breakdown_template->activity)}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Activity
            </a>
            <a href="{{ route('breakdown-template.index')}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Template List
            </a>
        </form>
    @endif
@stop

@section('body')
    <table class="table table-condensed table-striped table-hover">
        <thead></thead>
        <tbody>
        <tr>
            <th class="col-sm-2">Code</th>
            <td>{{$breakdown_template->code}}</td>
            <th class="col-sm-2">Activity</th>
            <td>
                <a href="{{route('std-activity.show', $breakdown_template->activity)}}">{{$breakdown_template->activity->name}}</a>
            </td>
        </tr>
        </tbody>
    </table>
    <h4 class="page-header">Resources</h4>
    <div class="form-group clearfix">
        @can('write', 'breakdown-template')
            <a href="/std-activity-resource/create?template={{$breakdown_template->id}}&project_id={{request('project_id')}}"
               class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-plus-circle"></i> Add Resource
            </a>
        @else
            @if ($breakdown_template->project)
                @can('breakdown_templates', $breakdown_template->project)
                    <a href="/std-activity-resource/create?template={{$breakdown_template->id}}&project_id={{$breakdown_template->project_id}}"
                       class="btn btn-primary btn-sm pull-right">
                        <i class="fa fa-plus-circle"></i> Add Resource
                    </a>
                @endcan
            @endif
        @endcan
    </div>

    @if ($breakdown_template->resources->count())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-6">Resource</th>
                <th class="col-xs-3">Equation</th>
                <th class="col-xs-3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($breakdown_template->resources as $resource)
                <tr>
                    <td class="col-xs-6">{{$resource->resource->name??''}}</td>
                    <td class="col-xs-3">{{$resource->equation??''}}</td>
                    <td class="col-xs-3">
                        @can('write', 'breakdown-template')
                            {{Form::model($resource, ['route' => ['std-activity-resource.destroy', $resource], 'method' => 'delete'])}}
                            <a href="{{route('std-activity-resource.edit', $resource)}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            @can('delete', 'breakdown-template')
                                <button class="btn btn-warning btn-sm"><i class="fa fa-trash"></i> Remove</button>
                            @endcan
                            {{Form::close()}}
                        @else
                            @if ($breakdown_template->project)
                                @can('breakdown_templates', $breakdown_template->project)
                                    {{Form::model($resource, ['route' => ['std-activity-resource.destroy', $resource], 'method' => 'delete'])}}
                                    <a href="{{route('std-activity-resource.edit', $resource)}}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <button class="btn btn-warning btn-sm"><i class="fa fa-trash"></i> Remove</button>
                                    {{Form::close()}}
                                @endcan
                            @endif
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No resources found</div>
    @endif
@stop
