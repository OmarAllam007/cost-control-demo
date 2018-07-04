@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            Breakdown template &mdash; {{$breakdown_template->name}}
            @if ($breakdown_template->project_id)
            &mdash; {{$breakdown_template->project->name}}
            @endif
        </h2>


        <div class="btn-toolbar">
            @if ($breakdown_template->project_id)
                @can('breakdown_templates', $breakdown_template->project)
                    <a href="/std-activity-resource/create?template={{$breakdown_template->id}}"
                       class="btn btn-primary btn-sm">
                        <i class="fa fa-plus-circle"></i> Add Resource
                    </a>
                @endcan

                <a href="{{ route('project.budget', $breakdown_template->project)}}" class="btn btn-sm btn-default">
                    <i class="fa fa-chevron-left"></i> Back to Project
                </a>
            @else
                @can('write', 'breakdown-template')
                    <a href="/std-activity-resource/create?template={{$breakdown_template->id}}"
                       class="btn btn-primary btn-sm">
                        <i class="fa fa-plus-circle"></i> Add Resource
                    </a>
                @endcan

                <a href="{{ route('breakdown-template.index')}}" class="btn btn-sm btn-default">
                    <i class="fa fa-chevron-left"></i> Back to Templates
                </a>
            @endif
        </div>
    </div>
@endsection

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

    <h3 class="page-header">Resources</h3>
    @if ($breakdown_template->resources->count())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Resource Code</th>
                <th>Resource Name</th>
                <th>Equation</th>
                <th>Remarks</th>
                <th>Productivity Ref</th>
                <th>Labours Count</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($breakdown_template->resources as $resource)
                <tr>
                    <td>{{$resource->resource->resource_code}}</td>
                    <td>{{$resource->resource->name}}</td>
                    <td>{{$resource->equation}}</td>
                    <td>{{$resource->remarks}}</td>
                    <td>{{$resource->productivity->csi_code??''}}</td>
                    <td>{{$resource->labor_count ?: ''}}</td>
                    <td>
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
                        @else
                            @can('write', 'breakdown-template')
                                {{Form::model($resource, ['route' => ['std-activity-resource.destroy', $resource], 'method' => 'delete'])}}
                                <a href="{{route('std-activity-resource.edit', $resource)}}"
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                @can('delete', 'breakdown-template')
                                    <button class="btn btn-warning btn-sm"><i class="fa fa-trash"></i> Remove</button>
                                @endcan
                                {{Form::close()}}
                            @endcan
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No resources found</div>
    @endif
@endsection
