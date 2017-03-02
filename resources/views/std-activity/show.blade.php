@extends('layouts.app')

@section('header')
    <h2>Std activity - {{$std_activity->name}}</h2>

    <form action="{{ route('std-activity.destroy', $std_activity)}}" class="pull-right" method="post">
        @can('write', 'std-activity')
            <a href="{{ route('std-activity.edit', $std_activity)}}" class="btn btn-sm btn-primary">
                <i class="fa fa-edit"></i> Edit
            </a>
        @endcan

        @can('delete', 'std-activity')
            {{csrf_field()}} {{method_field('delete')}}
            <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @endcan

        <a href="{{ route('std-activity.index')}}" class="btn btn-sm btn-default">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </form>
@stop

@section('body')
    <table class="table table-condensed">
        <tbody>
        <tr>
            <th>Division</th>
            <td>{{$std_activity->division->path}}</td>
        </tr>
        <tr>
            <th>Code</th>
            <td>{{$std_activity->code}}</td>
        </tr>
        @if ($std_activity->id_partial)
            <tr>
                <th>Partial ID</th>
                <td>{{$std_activity->id_partial}}</td>
            </tr>
        @endif
        </tbody>
    </table>

    @can('read', 'breakdown-templates')
        <h4 class="page-header">Breakdown Templates</h4>
        <div class="form-group clearfix">
            @can('write', 'breakdown-templates')
                <a href="{{route('breakdown-template.create', ['activity' => $std_activity->id])}}"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-plus-circle"></i> Add template
                </a>
            @endcan
        </div>

        @if ($std_activity->breakdowns()->public()->count())
            <table class="table table-condensed table-hover table-striped">
                <thead>
                <tr>
                    <td>Code</td>
                    <td>Name</td>
                    <td>Actions</td>
                </tr>
                </thead>
                <tbody>
                @foreach($std_activity->breakdowns()->public()->get() as $breakdown)
                    <tr>
                        <td>{{$breakdown->code}}</td>
                        <td>{{$breakdown->name}}</td>
                        <td>
                            {{Form::model($breakdown, ['method' => 'delete', 'route' => ['breakdown-template.destroy', $breakdown]])}}
                            <a href="{{route('breakdown-template.show', $breakdown)}}" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i> Show
                            </a>

                            @can('write', 'breakdown-templates')
                                <a href="{{route('breakdown-template.edit', $breakdown)}}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>
                            @endcan

                            @can('delete', 'breakdown-templates')
                                <button class="btn btn-warning btn-sm"><i class="fa fa-trash"></i> Remove</button>
                            @endcan
                            {{Form::close()}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> No breakdowns added</div>
        @endif
    @endcan
@stop
