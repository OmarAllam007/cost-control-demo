@extends('layouts.' . (request('print')? 'print' : 'app'))

@if(request('all'))
    @include('reports.all._budget_dry_discipline')
@endif

@section('title', 'Budget Cost By VS Dry Cost By Discipline &mdash; ' . $project->name)

@section('header')
    <div class="display-flex">
        <h2 class="flex">Budget Cost By VS Dry Cost By Discipline &mdash; {{$project->name}}</h2>

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
        @foreach($disciplines as $cost)
            <tr class="bg-{{$cost->difference > 0 ? 'danger' : ''}}">
                <td class="col-xs-3">{{$cost->type ?: 'General'}}</td>
                <td class="col-xs-2">{{number_format($cost->dry_cost, 2) }}</td>
                <td class="col-xs-2">{{number_format($cost->budget_cost ?: 0, 2)}}</td>
                <td class="col-xs-2">{{number_format($cost->difference, 2)}}</td>
                <td class="col-xs-2 {{$cost->increase > 0? 'text-danger' : 'text-success'}}">{{number_format($cost->increase, 2)}}%</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
        @php
            $diff = $disciplines->sum('difference');
            $dry_cost = $disciplines->sum('dry_cost');
            $increase = $dry_cost ? ($diff * 100 / $dry_cost) : 0;
        @endphp
        <tr class="bg-{{$diff > 0? 'danger' : 'success'}}" style="border-top: 3px solid #fff;">
            <th class="col-xs-3"><strong>Grand Total</strong></th>
            <th class="col-xs-2"><strong>{{number_format($dry_cost, 2)}}</strong></th>
            <th class="col-xs-2"><strong>{{number_format($disciplines->sum('budget_cost'), 2)}}</strong></th>
            <th class="col-xs-2 {{$diff > 0? 'text-danger' : ''}}">{{number_format($diff, 2)}}</th>
            <th class="col-xs-2 {{$increase > 0? 'text-danger' : ''}}">{{number_format($increase, 2)}}%</th>
        </tr>
        </tfoot>
    </table>
    <hr>
    <div class="row">
        <div class="col-sm-6">
            <h4 class="text-center">Budget Cost vs Dry Cost</h4>
            <div class="chart" id="compareChart"></div>
        </div>
        <div class="col-sm-6">
            <h4 class="text-center">Difference between Budget Cost and Dry Cost</h4>
            <div class="chart" id="differenceChart"></div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/c3.min.css">
    <style>
        .chart {
            min-height: 300px;
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
                    {!! $disciplines->pluck('dry_cost')->prepend('Dry Cost') !!},
                    {!! $disciplines->pluck('budget_cost')->prepend('Budget Cost') !!}
                ],
                type: 'bar'
            },
            axis: {
                x: {type: 'category', categories: {!! $disciplines->pluck('type') !!}},
                y: {tick: {format: d3.format(",.2f")}}
            },
            grid: {x: {show: true}, y: {show: true}}
        });

        c3.generate({
            bindto: '#differenceChart',
            data: {
                columns: [{!! $disciplines->pluck('difference')->prepend('Difference') !!}], type: 'bar'
            },
            axis: {
                x: {type: 'category', categories: {!! $disciplines->pluck('type') !!}},
                y: {tick: {format: d3.format(",.2f")}}
            },
            grid: {x: {show: true}, y: {show: true}},
            bar: {width: {ratio: 0.25}}
        });
    </script>
@endsection
