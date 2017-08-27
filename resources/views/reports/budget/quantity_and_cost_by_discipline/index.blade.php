@extends('layouts.' . (request('print')? 'print' : 'app'))

@if(request('all'))
    @include('reports.all._budget_dry_discipline')
@endif

@section('title', 'Qty &amp; Cost By Discipline &mdash; ' . $project->name)

@section('header')
    <div class="display-flex">
        <h2 class="flex">Qty &amp; Cost By Discipline &mdash; {{$project->name}}</h2>

        @if (!request('print'))
            <div class="btn-toolbar">
                <a href="?excel" class="btn btn-success btn-sm"><i class="fa fa-cloud-download"></i> Excel</a>
                <a href="?print=1&paint=cost-dry-discipline" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</a>
                <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('image')
    <img src="{{asset('images/reports/budgetdrydiscpline.jpg')}}">
@endsection

@section('body')
    <table class="table table-striped table-hover">
        <thead>
        <tr class="bg-primary">
            <th>Discipline</th>
            <th class="text-center">(Budget Cost &mdash; Dry Cost) * Budget Quantity</th>
            <th class="text-center">(Budget QTY &mdash; Dry QTY) * Budget cost</th>
        </tr>
        </thead>
        <tbody>
        @foreach($disciplines as $cost)
            <tr>
                <td>{{$cost->type ?: 'General'}}</td>
                <td class="text-center {{$cost->cost_diff < 0? 'text-danger' : 'text-success'}}">{{number_format($cost->cost_diff, 2) }}</td>
                <td class="text-center {{$cost->qty_diff < 0? 'text-danger' : 'text-success'}}">{{number_format($cost->qty_diff, 2)}}</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
        @php
            $qty_diff = $disciplines->sum('qty_diff');
            $cost_diff = $disciplines->sum('cost_diff');
        @endphp
        <tr>
            <th><strong>Grand Total</strong></th>
            <th class="text-center"><strong>{{number_format($cost_diff, 2)}}</strong></th>
            <th class="text-center"><strong>{{number_format($qty_diff, 2)}}</strong></th>
        </tr>
        </tfoot>
    </table>
    <hr>
    <div class="chart" id="compareChart"></div>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/c3.min.css">
    <style>
        .chart {
            min-height: 400px;
            margin-top: 40px;
        }

        tfoot tr {
            border-top: 3px solid #ddd;
        }
    </style>
@endsection

@section('javascript')
    <script src="/js/d3.min.js"></script>
    <script src="/js/c3.min.js"></script>

    <script>
        c3.generate({
            bindto: '#compareChart',
            data: {
                columns: [
                    {!! $disciplines->pluck('cost_diff')->prepend('(Budget Cost &mdash; Dry Cost) * Budget Quantity') !!},
                    {!! $disciplines->pluck('qty_diff')->prepend('(Budget QTY &mdash; Dry QTY) * Budget cost') !!}
                ],
                type: 'bar'
            },
            axis: {
                x: {type: 'category', categories: {!! $disciplines->pluck('type') !!}},
                y: {tick: {format: d3.format(",.2f")}}
            },
            grid: {x: {show: true}, y: {show: true}},
            bar: {width: 30}
        });
    </script>
@endsection
