@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._revised_boq')
@endif
@section('header')
    <h2 align="center">Revised Boq</h2>
    <div class="pull-right">
        <a href="?print=1&paint=revised" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/revised-boq.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed table-responsive">
        <thead class="output-cell">
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-3">BUILDING NAME</th>
            <th class="col-xs-3">REVISED BOQ</th>
            <th class="col-xs-2">ORIGINAL BOQ</th>
            <th class="col-xs-2">% Weight</th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr class="tbl-content">
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-3">{{$row['name']}}</td>
                <td class="col-xs-3">{{number_format($row['revised_boq'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['original_boq'],2)}}</td>
                <td class="col-xs-2">% {{number_format($row['weight'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2 output-cell"></td>
            <td class="col-xs-3 output-cell" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-3 output-cell">{{number_format($total['revised_boq'],2)}}</td>
            <td class="col-xs-2 output-cell">{{number_format($total['original_boq'],2)}}</td>
            <td class="col-xs-2 output-cell">% {{number_format($total['weight'],2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

    <div id="chart-div"></div>
    <?=  \Lava::render('PieChart', 'BOQ', 'chart-div') ?>

@endsection