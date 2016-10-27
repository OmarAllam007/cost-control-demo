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

    <table class="table table-condensed">
        <thead>
        <tr class="output-cell">
            <th class="col-xs-2">Code</th>
            <th class="col-xs-2">Building Name</th>
            <th class="col-xs-2">Dry Cost</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Difference</th>
            <th class="col-xs-2">(%) Increase</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr class="tbl-content">
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-2">{{$row['name']}}</td>
                <td class="col-xs-2">{{number_format($row['dry_cost'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['difference'],2)}}</td>
                <td class="col-xs-2">% {{intval($row['increase'])}}</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000" class="output-cell">
            <td class="col-xs-2"></td>
            <td class="col-xs-2">Grand Total</td>
            <td class="col-xs-2">{{number_format($total['total_dry'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['total_budget'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['difference'],2)}}</td>
            <td class="col-xs-2">% {{number_format($total['total_increase'])}}</td>
        </tr>
        </tbody>
    </table>
    <div id="chart-div"></div>
    <?=  \Lava::render('ColumnChart', 'BudgetCost', 'chart-div') ?>
<br>
    <div id="chart-div2"></div>
    <?=  \Lava::render('ColumnChart', 'Difference', 'chart-div2') ?>

    <div id="chart-div3"></div>
    <?=  \Lava::render('ColumnChart', 'Increase', 'chart-div3') ?>
@endsection