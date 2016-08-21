@extends('layouts.app')

@section('header')
    <h2>Breakdown template - {{$breakdown_template->name}}</h2>

    <form action="{{ route('breakdown-template.destroy', $breakdown_template)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('breakdown-template.edit', $breakdown_template)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>
            Edit</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('std-activity.show', $breakdown_template->activity)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i>
            Back</a>
    </form>
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
        <a href="{{route('std-activity-resource.create', ['template' => $breakdown_template->id])}}" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus-circle"></i>
            Add Resource</a>
    </div>

    @if ($breakdown_template->resources)
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <td class="col-md-7">Resource</td>
                <td>Equation</td>
                <td>Action</td>
            </tr>
            </thead>
            <tbody>
            @foreach($breakdown_template->resources as $resource)
                <tr>
                    <td>{{$resource->resource->name}}</td>
                    <td>{{$resource->equation}}</td>
                    <td>
                        {{Form::model($resource, ['route' => ['std-activity-resource.destroy', $resource], 'method' => 'delete'])}}
                        <a href="{{route('std-activity-resource.edit', $resource)}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        <button class="btn btn-warning btn-sm"><i class="fa fa-trash"></i> Remove</button>
                        {{Form::close()}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No resources found</div>
    @endif
@stop
