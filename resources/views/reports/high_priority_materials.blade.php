@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2>High Priority Materials</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/high-priority.jpg')}}">
@endsection
@section('body')
    <table class="table table-condensed table-striped table-bordered">
        <thead>
        <tr>
            <th class="col-xs-6">Description</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Budget Unit</th>
            <th class="col-xs-2">Unit</th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                <td class="col-xs-3">{{$row['name']}}</td>
                <td class="col-xs-3">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-3">{{number_format($row['budget_unit'],2)}}</td>
                <td class="col-xs-3">{{$row['unit']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
@stop