@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2>Budget Cost By Item break Down</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/costbybreakdown.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed">
        <thead>
        <tr class="output-cell">
            <th class="col-xs-3" >Code</th>
            <th class="col-xs-3" >Resource Type</th>
            <th class="col-xs-3" >Budget Cost</th>
            <th class="col-xs-3" >% Weight</th>

        </tr>
        </thead>
        <tbody>
        @foreach($bd_resource as $row)

            <tr class="tbl-content">
                <td class="col-xs-3">{{$row['resource_code']?:''}}</td>
                <td class="col-xs-3">{{$row['resource_type']}}</td>
                <td class="col-xs-3">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-3">{{number_format($row['weight'],2)}} %</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000" class="output-cell">
            <td class="col-xs-3"></td>
            <td class="col-xs-3">Grand Total</td>
            <td class="col-xs-3">{{number_format($total['total'])}}</td>
            <td class="col-xs-3">{{number_format($total['weight_total'])}} %</td>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="chart-div" style="width:800px; margin:0 auto;"></div><hr>
    <?=  \Lava::render('PieChart','BreakDown','chart-div') ?>
@endsection