@extends('layouts.app')
@section('header')
    <h1>Activity Resource Breakdown</h1>
    <a href="{{URL::previous()}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
{{$project->getRecourceBreakDown}}
@section('body')
    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Activity Name</th>
            <th class="col-xs-2">Cost Account</th>
            <th class="col-xs-2">Resource Name</th>
            <th class="col-xs-2">Price/Unit</th>
            <th class="col-xs-2">BUDGET UNIT</th>
            <th class="col-xs-2">Budget Cost</th>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($project->productivities as $productivity)
            <tr>

                <td class="col-xs-2" ></td>
                <td class="col-xs-2"></td>
                <td class="col-xs-2"></td>
                <td class="col-xs-2"></td>
                <td class="col-xs-2"></td>
                <td class="col-xs-2"></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection