@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._cost_dry')
@endif
@section('header')
    <h2 align="center">Quantity And Cost </h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-dry" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@stop

@section('body')
    <table class="table table-condensed ">
        <thead  class="output-cell">
        <tr>
            <th class="col-xs-4">Discipline</th>
            <th class="col-xs-4">(Budget Cost- Dry Cost) * Budget Quantity</th>
            <th class="col-xs-4">(Budget QTY- Dry QTY) * Budget cost</th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $key=>$row)
            <tr class="tbl-content">
                <td class="col-xs-4">{{$key}}</td>
                <td class="col-xs-4" @if($row ['left_eq']<0) style="color: #c42737;" @endif>{{number_format($row['left_eq'],2)}}</td>
                <td class="col-xs-4" @if($row ['right_eq']<0) style="color: #c42737;" @endif>{{number_format($row['right_eq'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-4 output-cell" style="font-weight: 800">Total</td>
            <td class="col-xs-4 output-cell" >{{number_format($total['left_eq'],2)}}</td>
            <td class="col-xs-4 output-cell" >{{number_format($total['right_eq'],2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection