@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_dry_discipline')
@endif
@section('header')
    <h2>Budget Cost By VS Dry Cost By Discipline</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-dry-discipline" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/budgetdrydiscpline.jpg')}}">
@endsection
@section('body')
    <table class="table table-condensed">
        <thead  class="output-cell">
        <tr>

            <th class="col-xs-3">Discipline</th>
            <th class="col-xs-2">Dry Cost</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Difference</th>
            <th class="col-xs-2">Increase</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $key=>$row)

            <tr class="tbl-content">

                <td class="col-xs-3">{{$key}}</td>
                <td class="col-xs-2">{{number_format($row['dry'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['cost'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['difference'],2)}}</td>
                <td class="col-xs-2">%{{number_format($row['increase'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-3 output-cell" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-2 output-cell">{{number_format($total['dry'],2)}}</td>
            <td class="col-xs-2 output-cell">{{number_format($total['budget'],2)}}</td>
            <td class="col-xs-2 output-cell">{{number_format($total['difference'],2)}}</td>
            <td class="col-xs-2 output-cell">% {{number_format($total['increase'],2)}}</td>
        </tr>
        </tbody>
    </table>
    <hr>
    <div id="chart-div" ></div>
    <?=  \Lava::render('ColumnChart', 'BudgetCost', 'chart-div') ?>


    <div id="chart-div2"></div>
    <?=  \Lava::render('ColumnChart', 'Difference', 'chart-div2') ?>
@endsection