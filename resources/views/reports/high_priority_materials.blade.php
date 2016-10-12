@extends('layouts.app')
@section('header')
    <h2>High Priority Materials</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')
    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-3">Description</th>
            <th class="col-xs-3">Budget Cost</th>
            <th class="col-xs-3">Budget Unit</th>
            <th class="col-xs-3">Unit</th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                <td class="col-xs-3">{{$row['name']}}</td>
                <td class="col-xs-3">{{$row['budget_cost']}}</td>
                <td class="col-xs-3">{{$row['budget_unit']}}</td>
                <td class="col-xs-3">{{$row['unit']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
@stop