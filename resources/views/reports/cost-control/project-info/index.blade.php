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

            <section class="info-section">
               @include('reports.cost-control.project-info.contract-info')
            </section>

            <section class="info-section">
                @include('reports.cost-control.project-info.revised_contract-info')
            </section>

            <section class="info-section" id="cost-summary">
                <h4 class="section-header">Cost Summary</h4>
                @include('reports.partials.cost-summary', $costSummary)
            </section>


            <section class="info-section" class="card-group">
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

        dl {
            display: flex;
            font-size: 12px;
            margin-bottom: 5px;
            box-shadow: 0 2px 4px 0 rgba(160, 240, 237, 0.5);
        }

        dt {
            background: #64D5CA;
            padding: 5px;
            text-align: right;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-weight: 700;
            width: 40%;
        }

        dd {
            background: #A0F0ED;
            padding: 5px;
            flex: 1;
            font-weight: 700;
        }

        .section-header {
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            padding: 7px;
            background: #64D5CA;
            margin-bottom: 5px;
        }

        .info-section {
            margin-bottom: 25px;
        }
    </style>
@append