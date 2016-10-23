@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2>Budget Cost By VS Dry Cost By Building</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/budgetdrybuilding.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed ">
        <thead>
        <tr class="row-shadow">
            <th class="col-xs-2" style="background-color:#446CB3; color: white">Code</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">Building Name</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">Dry Cost</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">Budget Cost</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">Difference</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">(%) Increase</th>
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
            <td class="col-xs-2">{{number_format($total['total_dry'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['total_budget'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['difference'],2)}}</td>
            <td class="col-xs-2">% {{number_format($total['total_increase'])}}</td>
        </tr>
        </tbody>
    </table>

@endsection