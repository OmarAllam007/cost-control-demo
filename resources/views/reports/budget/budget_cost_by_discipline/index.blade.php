@extends('layouts.' . (request()->has('print') ? 'print' : 'app'))

@section('title', 'Budget Cost By Discipline')

@section('header')
    <div class="display-flex">
        <h4 class="flex">Budget Cost By Discipline &mdash; {{$project->name}}</h4>

        @if (!request()->has('print'))
            <div>
                <a href="?excel" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> Export</a>
                <a href="?print=1&paint=survey" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print</a>
                <a href="{{route('project.show', $project)}}#Reports" class="btn btn-sm btn-default"><i
                            class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('body')
    <table class="table table-condensed table-bordered table-striped" id="report-table">
        <thead>
        <tr class="bg-primary">
            <th class="col-sm-3">Discipline</th>
            <th class="col-sm-2">Budget Cost</th>
            <th class="col-sm-3">Weight</th>
        </tr>
        </thead>
        <tbody>
        @foreach($costs as $cost)
            <tr>
                <td>{{strtoupper($cost->discipline)}}</td>
                <td>{{number_format($cost->budget_cost, 2)}}</td>
                <td>%{{number_format($cost->weight, 2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="bg-primary">
            <th>Grand Total</th>
            <th>{{number_format($total_cost, 2)}}</th>
            <th>&nbsp;</th>
        </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-sm-6">
            <h4 class="text-center">Budget Cost</h4>
            <div class="chart" id="bar-chart-area"></div>
        </div>
        <div class="col-sm-6">
            <h4 class="text-center">Weight</h4>
            <div class="chart" id="pie-chart-area"></div>
        </div>
    </div>

@endsection

@section('javascript')
    @php
        $columns = $costs->map(function($discipline) {
            return [$discipline->discipline, $discipline->budget_cost];
        });
    @endphp
    <script src="/js/d3.min.js"></script>
    <script src="/js/c3.min.js"></script>
    <script>
        c3.generate({
            bindto: '#bar-chart-area',
            data: {
                columns: {!! $columns !!},
                type: 'bar',
            },
            bar: {
                width: 50
            },
            axis: {
              /*  x: {
                    type: 'category',
                    categories: {!! $costs->pluck('discipline') !!}
                },*/
                y : {
                    tick: {
                        format: d3.format(",.2f")
                    }
                }
            },
            grid: {
                x: {show: true},
                y: {show: true}
            }
        });

        c3.generate({
            bindto: '#pie-chart-area',
            data: {
                columns: {!! $costs->map(function($cost) { return [$cost->discipline, $cost->weight]; })!!},
                type: 'pie',
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $costs->pluck('discipline') !!}
                },
                y : {
                    tick: {
                        format: d3.format(",.2f")
                    }
                }
            },
            grid: {
                x: {show: true},
                y: {show: true}
            }
        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/c3.min.css">
    <style>
        #report-table tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-table tbody tr.highlighted > td,
        #report-table thead tr.highlighted > th {
            background-color: #ffc;
        }

        .chart {
            min-height: 400px
        }
    </style>
@endsection