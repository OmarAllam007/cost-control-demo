@extends('layouts.app')

@section('header')
    <h2>Std activity - {{$std_activity->name}}</h2>

    <form action="{{ route('std-activity.destroy', $std_activity)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('std-activity.edit', $std_activity)}}" class="btn btn-sm btn-primary">
            <i class="fa fa-edit"></i> Edit
        </a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('std-activity.index')}}" class="btn btn-sm btn-default">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </form>
@stop

@section('body')
    <table class="table table-condensed">
        <tr>
            <th>Division</th>
            <td>{{$std_activity->division->path}}</td>
        </tr>
        <tr>
            <th>Code</th>
            <td>{{$std_activity->code}}</td>
        </tr>
        <tr>
            <th>Partial ID</th>
            <td>{{$std_activity->id_partial}}</td>
        </tr>
    </table>
@stop
