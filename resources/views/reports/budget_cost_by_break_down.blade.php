@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif
@section('header')
    <h2>Budget Cost By Item break Down</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/costbybreakdown.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed">
        <thead class="output-cell">
        <tr >
            <th class="col-xs-4" >Resource Type</th>
            <th class="col-xs-4" >Budget Cost</th>
            <th class="col-xs-4" >% Weight</th>

        </tr>
        </thead>
        <tbody>
        @foreach($types as $key=>$row)

            <tr class="tbl-content">
                <td class="col-xs-4">{{$key}}</td>
                <td class="col-xs-4">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-4">{{number_format($row['weight'],2)}} %</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-4 output-cell">Grand Total</td>
            <td class="col-xs-4 output-cell">{{number_format($total)}}</td>
            <td class="col-xs-4 output-cell">{{number_format($totalWeight)}} %</td>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="chart-div" style="width:800px; margin:0 auto;"></div><hr>
    <?=  \Lava::render('PieChart','BreakDown','chart-div') ?>
@endsection