@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2>Budget Cost By Discipline</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-discipline" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/costbydiscipline.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed">
        <thead  class="output-cell">
        <tr>

            <th class="col-xs-4">Discipline</th>
            <th class="col-xs-4">Budget Cost</th>
            <th class="col-xs-4">Weight</th>
        </tr>
        </thead>
        <tbody>
        @foreach($survey as $row)
            <tr class="tbl-content">

                <td class="col-xs-4">{{$row['name']}}</td>
                <td class="col-xs-4">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-4">%{{number_format($row['weight'])}}</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000" >

            <td class="col-xs-4 output-cell" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-4 output-cell">{{number_format($total['total'],2)}}</td>
            <td class="col-xs-4 output-cell" style="font-style: italic">{{number_format($total['weight_total'],2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>
    <div id="chart-div" style="width:800px; margin:0 auto;"></div><hr>
    <?=  \Lava::render('PieChart','Cost','chart-div') ?>

    <div id="chart-div2" style="width:800px; margin:0 auto;"></div>
    <?= \Lava::render('ColumnChart','BudgetCost','chart-div2') ?>
@endsection