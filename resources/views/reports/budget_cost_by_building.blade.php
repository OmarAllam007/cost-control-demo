@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2>Budget Cost By Building</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/costbybuilding.jpg')}}">
@endsection
@section('body')
    <table class="table table-condensed table-fixed">
        <thead>
        <tr class="output-cell">
            <th class="col-xs-3">Code</th>
            <th class="col-xs-3">Building Name</th>
            <th class="col-xs-3">Budget Cost</th>
            <th class="col-xs-3">Weight</th>

            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr class="tbl-content">
                <td class="col-xs-3">{{$row['code']}}</td>
                <td class="col-xs-3">{{$row['name']}}</td>
                <td class="col-xs-3">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-3">%{{number_format($row['weight'],2)}}</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000" class="output-cell">
            <td class="col-xs-3"></td>
            <td class="col-xs-3" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-3">{{number_format($total['total'],2)}}</td>
            <td class="col-xs-3">% {{number_format($total['weight'],2)}}</td>
        </tr>
        </tbody>
    </table>

@endsection