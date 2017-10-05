@extends('layouts.' . (request('print')? 'print' : 'app'))

@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif

@section('header')
    <div class="display-flex">
        <h2 class="flex">High Priority Materials Report &mdash; {{$project->name}}</h2>

        @if (!request('print'))
        <div class="btn-toolbar">
            <a href="?excel" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> Export</a>
            <a href="?print=1" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Print</a>
            <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
        @endif
    </div>
@endsection

@section('body')

    <div class="row">
        <section class="report-table col-sm-12">
            <table class="table table-bordered table-hover">
                <thead>
                <tr class="bg-primary">
                    <th class="col-sm-4">Resource Name</th>
                    <th class="col-sm-2">Resource Code</th>
                    <th class="col-sm-2">Budget Unit</th>
                    <th class="col-sm-2">Budget Cost</th>
                    <th class="col-sm-2">Weight (%)</th>
                </tr>
                </thead>
            </table>
            <section class="vertical-scroll">
                <table class="table table-bordered table-condensed table-hover" id="report-body">
                    <tbody>
                    @foreach ($tree as $group)
                        <tr class="bg-info">
                            <th colspan="3">
                                <a href="" class="group-name" data-target="{{slug($group['name'])}}">{{$group['name']}}</a>
                            </th>
                            <th class="text-right">{{number_format($group['budget_cost'], 2)}}</th>
                            <th>{{number_format($group['weight'], 2)}}%</th>
                        </tr>

                        @foreach($group['resources'] as $resource)
                            <tr class="{{slug($group['name'])}} collapse">
                                <td class="col-sm-4">{{$resource->name}}</td>
                                <td class="col-sm-2">{{$resource->resource_code}}</td>
                                <td class="col-sm-2 text-right">{{number_format($resource->budget_unit, 2)}}</td>
                                <td class="col-sm-2 text-right">{{number_format($resource->budget_cost, 2)}}</td>
                                <td class="col-sm-2">{{number_format($resource->weight, 2)}}%</td>
                            </tr>
                        @endforeach

                    @endforeach
                    </tbody>
                </table>
            </section>
        </section>

        <section class="chart-area col-sm-8 col-sm-offset-2">
            <h4 class="text-center">High priority materials</h4>
            <div id="high-priority-chart"></div>
        </section>
    </div>



@endsection

@section('javascript')
    @php
        $chartLabels = $tree->pluck('name');
        $chartValues = $tree->pluck('budget_cost')->prepend('Budget Cost');
    @endphp
    <script src="/js/d3.min.js"></script>
    <script src="/js/c3.min.js"></script>

    <script>
        c3.generate({
            bindto: '#high-priority-chart',
            data: {
                type: 'bar',
                columns: [{!! $chartValues !!}]
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $chartLabels !!}
                }
            },
            grid: {
                x: {show: true},
                y: {show: true},
            },
            bar: {
                width: 40
            }
        });

        $('.group-name').on('click', function (e) {
            e.preventDefault();

            let target = $(this).data('target');
            $(`.${target}`).toggleClass('in');
        });
    </script>
@endsection

@section('css')
    <style>
        .vertical-scroll {
            max-height: 500px;
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        #report-body tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-body tbody tr.highlighted > td {
            background-color: #ffc;
        }

        .chart-area {
            margin-top: 30px;
        }

        #high-priority-chart {
            min-height: 400px;
        }
    </style>
    <link rel="stylesheet" href="/css/c3.min.css">
@endsection
