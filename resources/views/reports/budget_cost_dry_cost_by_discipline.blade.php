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

@section('css')

@endsection

@section('body')
    <table class="table table-condensed">
        <thead>
        <tr class="bg-primary">
            <th class="col-xs-3">Discipline</th>
            <th class="col-xs-2">Dry Cost</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Difference</th>
            <th class="col-xs-2">Increase</th>
        </tr>
        </thead>
        <tbody>
        @foreach($budgetData as $type => $cost)
            @php
                $dry_cost = $boqData[$type]->dry_cost ?? 0;
                $budget_cost = $cost->budget_cost;
                $diff = $dry_cost - $budget_cost;
                $increase = $dry_cost? $diff * 100 / $dry_cost : 0;
            @endphp
            <tr class="bg-{{$diff < 0? 'danger' : ''}}">
                <td class="col-xs-3">{{$cost->type ?: 'General'}}</td>
                <td class="col-xs-2">{{number_format($dry_cost, 2) }}</td>
                <td class="col-xs-2">{{number_format($budget_cost ?: 0, 2)}}</td>
                <td class="col-xs-2">{{number_format($diff, 2)}}</td>
                <td class="col-xs-2 {{$increase < 0? 'text-danger' : 'text-success'}}">{{number_format($increase, 2)}} %</td>
            </tr>
        @endforeach
        <tfoot>
        @php
        $total_dry = $boqData->sum('dry_cost');
        $total_budget = $budgetData->sum('budget_cost');
        $diff = $total_dry - $total_budget;
        $increase = $total_dry? $diff * 100 / $total_dry : 0;
        @endphp
        <tr class="bg-{{$diff < 0? 'danger' : 'success'}}" style="border-top: 2px solid #ccc">
            <th class="col-xs-3"><strong>Grand Total</strong></th>
            <th class="col-xs-2"><strong>{{number_format($total_dry ,2)}}</strong></th>
            <th class="col-xs-2"><strong>{{number_format($total_budget,2)}}</strong></th>
            <th class="col-xs-2 {{$diff < 0? 'text-danger' : 'text-success'}}">{{number_format($diff,2)}}</th>
            <th class="col-xs-2 {{$increase < 0? 'text-danger' : 'text-success'}}">{{number_format($increase,2)}}%</th>
        </tr>
        </tbody>
        </tfoot>
    </table>
    <hr>
    <div id="chart-div" ></div>
@endsection