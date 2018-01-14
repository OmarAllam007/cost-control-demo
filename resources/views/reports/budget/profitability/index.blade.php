@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            Profitability Index &mdash; {{$project->name}}
        </h2>
        <div>
            <a href="?excel" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> Export</a>
            <a href="?print=1&paint=wbs" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
            <a href="{{route('project.show', $project)}}#Reports" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
    </div>
@endsection

@section('body')
    <section class="clearfix">
        <section class="pull-left">
            <table class="table table-condensed table-bordered" id="report-header">
                <thead>
                <tr class="bg-primary"><th>&nbsp;</th></tr>
                <tr><th>Total Project Budget</th></tr>
                <tr><th>Original Contract Amount</th></tr>
                <tr><th>Changer Order Amount</th></tr>
                <tr><th>Total Revised Contract Amount</th></tr>
                <tr><th>Profitability</th></tr>
                <tr><th>Profitability Index</th></tr>
                <tr><th>Variance</th></tr>
                </thead>
            </table>
        </section>

        <section class="horizontal-scroll display-flex">
            <div class="scroll-area" style="width: {{$revisions->count() * 300}}px">
                @foreach($revisions as $revision)
                    <article class="pull-left revision-info">
                        <table class="table table-condensed table-bordered">
                            <tbody>
                            <tr class="bg-primary"><th class="text-center">{{$revision->name}}</th></tr>
                            <tr><td>{{number_format($revision->budget_cost, 2)}}</td></tr>
                            <tr><td>{{number_format($revision->original_contract_amount, 2)}}</td></tr>
                            <tr><td>{{number_format($revision->change_order_amount, 2)}}</td></tr>
                            <tr><td>{{number_format($revision->revised_contract_amount, 2)}}</td></tr>
                            <tr><td class="{{$revision->profitability > 0? 'text-success' : 'text-danger'}}">{{number_format($revision->profitability, 2)}}</td></tr>
                            <tr><td class="{{$revision->profitability_index > 0? 'text-success' : 'text-danger'}}">{{number_format($revision->profitability_index, 2)}}%</td></tr>
                            <tr><td>{{number_format($revision->variance, 2)}}%</td></tr>
                            </tbody>
                        </table>
                    </article>
                @endforeach
            </div>
        </section>
    </section>

    <h4 class="text-center">Profitability Trend</h4>
    <div id="index-trend-chart" style="min-height: 300px;"></div>

    <h4 class="text-center">Budget Amount vs Contract Amount</h4>
    <div id="diff-chart" style="min-height: 300px;"></div>
@endsection

@section('javascript')
    <script src="/js/d3.min.js"></script>
    <script src="/js/c3.min.js"></script>

        <script>
            c3.generate({
                bindto : '#index-trend-chart',
                data: {
                    type: 'line',
                    columns: [{!! $revisions->pluck('profitability_index')->prepend('Profitability Index') !!}]
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: {!! $revisions->pluck('name') !!}
                    },
                    y: {
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
                bindto : '#diff-chart',
                data: {
                    type: 'bar',
                    columns: [
                        {!! $revisions->pluck('revised_contract_amount')->prepend('Revised Contract Amount') !!},
                        {!! $revisions->pluck('budget_cost')->prepend('Project Budget') !!}
                    ]
                },
                axis: {
                    x: { type: 'category', categories: {!! $revisions->pluck('name') !!} },
                    y: {
                        tick: { format: d3.format(".0%") }
                    },

                    rotated: true
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
        .horizontal-scroll {
            overflow-x: auto;
            min-width: 300px;
        }

        .revision-info {
            width: 300px;
        }

        #report-header {
            width: 300px;
        }

        .table tr td, .table tr th {
            min-height: 35px;
            max-height: 35px;
            height: 35px;
        }

        table#report-header.table > thead > tr > th {
            text-align: right;
        }

        #report-body tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-body tbody tr.highlighted > td {
            background-color: #ffc;
        }
    </style>
@endsection