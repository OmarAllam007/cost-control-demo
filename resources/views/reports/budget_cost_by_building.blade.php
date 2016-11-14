@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_building')
@endif
@section('header')
    <h2>Budget Cost By Building</h2>
    <div class="pull-right">
        <a href="?print=1&paint=budget-building" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/costbybuilding.jpg')}}">
@endsection
@section('body')
    <table class="table table-condensed table-responsive">
        <thead class="output-cell">
        <tr>
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
        <tr style="border-top: solid #000000">
            <td class="col-xs-3 output-cell"></td>
            <td class="col-xs-3 output-cell" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-3 output-cell">{{number_format($total['total'],2)}}</td>
            <td class="col-xs-3 output-cell">% {{number_format($total['weight'],2)}}</td>
        </tr>
        </tbody>
    </table>


    <div id="chart-div" style="width:800px; margin:0 auto;"></div>
    <hr>
    <?=  \Lava::render('PieChart', 'BOQ', 'chart-div') ?>

    <div id="chart-div2" style="width:800px; margin:0 auto;"></div>
    <?= \Lava::render('ColumnChart', 'BudgetCost', 'chart-div2') ?>
@endsection
