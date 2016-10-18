@extends('layouts.app')
@section('header')
    <h2>Budget Cost By Discipline</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-2">Discipline</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Weight</th>
            <th class="col-xs-2"></th>
            <th class="col-xs-2"></th>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($survey as $row)

            <tr>
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-2">{{$row['name']}}</td>
                <td class="col-xs-2">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-2">%{{number_format($row['weight'])}}</td>
                <td class="col-xs-2"></td>
                <td class="col-xs-2"></td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-2" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-2">{{number_format($total['total'],2)}}</td>
            <td class="col-xs-2" style="font-style: italic">{{number_format($total['weight_total'],2)}}</td>
            <td class="col-xs-2"></td>
            <td class="col-xs-2"></td>
            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection