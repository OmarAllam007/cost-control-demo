@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif

@section('title', 'Project Information')

@section('header')
    <h2>{{$project->name}} &mdash; Project Information Report</h2>

    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12 col-xl-8 col-xl-offset-2">

            <section id="cost-summary">
                @include('reports.partials.cost-summary', $costSummary)
            </section>


            <div class="row">
                <div class="col-sm-12 col-lg-10 col-lg-offset-1">
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

        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
@append