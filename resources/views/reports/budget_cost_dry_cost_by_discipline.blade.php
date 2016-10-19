@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2>Budget Cost By VS Dry Cost By Discipline</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/budgetdrydiscpline.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-2">Discipline</th>
            <th class="col-xs-2">Dry Cost</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Difference</th>
            <th class="col-xs-2">Increase</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr>
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-2">{{$row['name']}}</td>
                <td class="col-xs-2">{{number_format($row['dry_cost'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['difference'],2)}}</td>
                <td class="col-xs-2">%{{number_format($row['increase'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-2" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-2">{{number_format($total['dry_cost'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['budget_cost'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['difference'],2)}}</td>
            <td class="col-xs-2">% {{number_format($total['increase'],2)}}</td>
            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection