@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2 align="center">Quantity And Cost </h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@stop
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr style="background-color: yellow">
            <th class="col-xs-4">Discipline</th>
            <th class="col-xs-4">(Budget Cost- Dry Cost) * DRY Qty</th>
            <th class="col-xs-4">(Budget QTY- Dry QTY) * Budget cost</th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr>
                <td class="col-xs-4">{{$row['name']}}</td>
                <td class="col-xs-4">{{number_format($row['dry_qty_eq'],2)}}</td>
                <td class="col-xs-4">{{number_format($row['budget_cost_eq'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-4" style="font-weight: 800">Total</td>
            <td class="col-xs-4">{{number_format($total['dry_qty_eq'],2)}}</td>
            <td class="col-xs-4">{{number_format($total['budget_cost_eq'],2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection