@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2>Budget Cost By Discipline</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/costbydiscipline.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr class="row-shadow">
            <th class="col-xs-3" style="background-color:#446CB3; color: white">Code</th>
            <th class="col-xs-3" style="background-color:#446CB3; color: white">Discipline</th>
            <th class="col-xs-3" style="background-color:#446CB3; color: white">Budget Cost</th>
            <th class="col-xs-3" style="background-color:#446CB3; color: white">Weight</th>
        </tr>
        </thead>
        <tbody>
        @foreach($survey as $row)
            <tr>
                <td class="col-xs-3">{{$row['code']}}</td>
                <td class="col-xs-3">{{$row['name']}}</td>
                <td class="col-xs-3">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-3">%{{number_format($row['weight'])}}</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-3"></td>
            <td class="col-xs-3" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-3">{{number_format($total['total'],2)}}</td>
            <td class="col-xs-3" style="font-style: italic">{{number_format($total['weight_total'],2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection