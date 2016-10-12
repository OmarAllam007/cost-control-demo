@extends('layouts.app')
@section('header')
    <h2>Budget Cost By VS Dry Cost</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-2">Building Name</th>
            <th class="col-xs-2">Dry Cost</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Difference</th>
            <th class="col-xs-2">(%) Increase</th>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr>
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-2">{{$row['name']}}</td>
                <td class="col-xs-2">{{$row['dry_cost']}}</td>
                <td class="col-xs-2">{{$row['budget_cost']}}</td>
                <td class="col-xs-2">{{$row['difference']}}</td>
                <td class="col-xs-2">% {{intval($row['increase'])}}</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-2">Grand Total</td>
            <td class="col-xs-2">{{$total['total_dry']}}</td>
            <td class="col-xs-2">{{$total['total_budget']}}</td>
            <td class="col-xs-2">{{$total['difference']}}</td>
            <td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>
        </tr>
        </tbody>
    </table>

@endsection