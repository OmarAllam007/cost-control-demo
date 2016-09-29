@extends('layouts.app')

@section('header')
<h2>Productivity</h2>

<form action="{{ route('productivity.destroy', $productivity)}}" class="pull-right" method="post">
    {{csrf_field()}} {{method_field('delete')}}

    <a href="{{ route('productivity.edit', $productivity)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
    <a href="{{ route('productivity.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
</form>
@stop

@section('body')
    <table class="table table-condensed">
        <tbody>
        <tr>
            <th>Project</th>
            <td>{{$project->name}}</td>
        </tr>
        <tr>
            <th>Productivity Code</th>
            <td>{{$productivity->code}}</td>
        </tr>

        </tbody>
    </table>

    <h4 class="page-header">Productivity</h4>
    {{--<div class="form-group clearfix">--}}
        {{--<a href="{{route('breakdown-template.create', ['activity' => $productivity->id])}}" class="btn btn-primary pull-right"><i class="fa fa-plus-circle"></i>--}}
            {{--Add template</a>--}}
    {{--</div>--}}

@stop

