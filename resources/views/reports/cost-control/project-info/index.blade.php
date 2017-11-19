@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif

@section('title', 'Project Information')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Project Information Report</h2>

        <div class="btn-toolbar">
            <a href="?excel" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-cloud-download"></i> Excel</a>
            <a href="?print=1" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Print</a>
            <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1">

            <section>
                <h4 class="section-header">Contract information</h4>

                <div class="row">
                    <dl class="col-sm-4">
                        <dt></dt>
                        <dd></dd>
                    </dl>

                    <dl class="col-sm-4">
                        <dt></dt>
                        <dd></dd>
                    </dl>

                    <dl class="col-sm-4">
                        <dt></dt>
                        <dd></dd>
                    </dl>

                    <dl class="col-sm-4">
                        <dt></dt>
                        <dd></dd>
                    </dl>

                    <dl class="col-sm-4">
                        <dt></dt>
                        <dd></dd>
                    </dl>

                    <dl class="col-sm-4">
                        <dt></dt>
                        <dd></dd>
                    </dl>
                </div>
            </section>

            <section id="cost-summary">
                <h4 class="section-header">Cost Summary</h4>
                @include('reports.partials.cost-summary', $costSummary)
            </section>


            <section class="card-group">
                @include('reports.cost-control.project-info.cpi-chart')
                @include('reports.cost-control.project-info.spi-chart')
                @include('reports.cost-control.project-info.waste_index_chart')
                @include('reports.cost-control.project-info.productivity_index_chart')
                @include('reports.cost-control.project-info.cost_progress_charts')
                @include('reports.cost-control.project-info.actual_revenue_chart')
            </section>


        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
@append

@section('css')
    <style>
        .card-group-item {
            padding: 10px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.12), 0 2px 4px 0 rgba(0, 0, 0, 0.08);
            border-radius: 5px;
        }

        .card-group-item:nth-child(even) {
            background: #F3F7F9;
        }

        .card-group-item-heading {
            color: #70818a;
            font-size: 12px;
            font-weight: 700;
        }

        .br-1 {
            border-right: 1px solid #dedede;
        }

        .cost-summary-table {
            font-size: 11px;
        }
    </style>
@append