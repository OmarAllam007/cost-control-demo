@php $print = $print ?? request()->exists('print') @endphp
@extends('layouts.' . ($print? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif

@section('title', 'Project Dashboard')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            @if (!$print) {{$project->name}} &mdash; @endif
            Project Dashboard
        </h2>

        @if(! $print)
            <div class="btn-toolbar">
                {{--<a href="?excel" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-cloud-download"></i>
                    Excel</a>--}}
                <a href="?refresh" class="btn btn-sm btn-default"><i class="fa fa-refresh"></i> Refresh</a>
                <a href="?pdf" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> PDF</a>
                <a href="?print=1" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Print</a>
                <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                            class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('body')
    @if (!$print)
        <form action="" method="get" class="row">
            <div class="form-group col-sm-4">
                <select name="period" id="" class="form-control" title="Select reporting period">
                    <option value="">-- Select Period --</option>
                    @foreach($periods as $p)
                        <option value="{{$p->id}}" {{$p->id == $period->id? 'selected' : ''}}>{{$p->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button class="button btn btn-primary"><i class="fa fa-check"></i> Update</button>
            </div>
        </form>
    @endif

    @include('reports.cost-control.project-info.contract-info')

    @include('reports.cost-control.project-info.revised_contract-info')

    @include('reports.cost-control.project-info.budget-info')

    @include('reports.cost-control.project-info.cost-info')

    @include('reports.partials.cost-summary', $costSummary)

    <section class="row info-section">
        <div class="col-md-{{$print? 12 : 6}}">
            @include('reports.cost-control.project-info.cpi-chart')
        </div>
        <div class="col-md-{{$print? 12 : 6}}">
            @include('reports.cost-control.project-info.spi-chart')
        </div>
        <div class="col-md-{{$print? 12 : 6}}">
            @include('reports.cost-control.project-info.waste_index_chart')
        </div>
        <div class="col-md-{{$print? 12 : 6}}">
            @include('reports.cost-control.project-info.productivity_index_chart')
        </div>
        <div class="col-md-12">
            @include('reports.cost-control.project-info.cost_progress_charts')
        </div>
        <div class="col-md-12">
            @include('reports.cost-control.project-info.actual_revenue_chart')
        </div>
    </section>

@endsection

@section('javascript')
    <script src="{{asset('/js/cost-info-charts.js')}}"></script>
@append

@section('css')
    <style>
        .card-group-item .pie-chart canvas {
            max-width: initial;
        }
    </style>
    {{--<style>--}}
    {{--.card-group-item {--}}
    {{--padding: 10px;--}}
    {{--margin-bottom: 15px;--}}
    {{--box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.12), 0 2px 4px 0 rgba(0, 0, 0, 0.08);--}}
    {{--border-radius: 5px;--}}
    {{--}--}}

    {{--.card-group-item:nth-child(even) {--}}
    {{--background: #F3F7F9;--}}
    {{--}--}}

    {{--.card-group-item-heading {--}}
    {{--color: #70818a;--}}
    {{--font-size: 12px;--}}
    {{--font-weight: 700;--}}
    {{--}--}}

    {{--.br-1 {--}}
    {{--border-right: 1px solid #dedede;--}}
    {{--}--}}

    {{--.cost-summary-table {--}}
    {{--font-size: 12px;--}}
    {{--}--}}

    {{--.budget-info {--}}
    {{--font-size: 12px;--}}
    {{--box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.12), 0 2px 4px 0 rgba(0, 0, 0, 0.08);--}}
    {{--}--}}

    {{--.section-header {--}}
    {{--font-size: 14px;--}}
    {{--color: #fff;--}}
    {{--text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);--}}
    {{--font-weight: 700;--}}
    {{--text-transform: uppercase;--}}
    {{--padding: 7px;--}}
    {{--background: #64D5CA;--}}
    {{--margin-bottom: 5px;--}}
    {{--letter-spacing: 1.2px;--}}
    {{--}--}}

    {{--.info-section {--}}
    {{--margin-bottom: 25px;--}}
    {{--}--}}

    {{--.revision-0 {--}}
    {{--background: #A0F0ED;--}}
    {{--border-right: 1px solid #fff;--}}
    {{--border-bottom: 1px solid #fff;--}}
    {{--color: #444;--}}
    {{--}--}}

    {{--.revision-1 {--}}
    {{--background: #64D5CA;--}}
    {{--border-bottom: 1px solid #fff;--}}
    {{--color: #444;--}}
    {{--}--}}

    {{--.mb-1 {--}}
    {{--margin-bottom: 1px;--}}
    {{--}--}}

    {{--.no-shadow {--}}
    {{--box-shadow: none;--}}
    {{--}--}}

    {{--.text-warning {--}}
    {{--color: #cd7920;--}}
    {{--}--}}
    {{--</style>--}}
@append