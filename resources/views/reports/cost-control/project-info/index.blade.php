@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif
@section('header')
    <h2>{{$project->name}} - Project Information Report</h2>

    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2">

            <section id="cost-summary">
                @include('reports.partials.cost-summary', $costSummary)
            </section>

            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">CPI</div>
                </div>

                <div class="panel-body">
                    <div class="chart"
                         id="cpiChart"
                         data-type="line"
                         data-labels="{{$cpiTrend->pluck('p_name')}}"
                         data-datasets="[{{json_encode([
                                'label' => 'CPI', 'data' => $cpiTrend->pluck('value'),
                                'backgroundColor' => '#F0FFF3',
                                'borderColor' => '#8ed3d8'
                            ])}}]"
                         style="height: 200px"
                    ></div>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Waste Index</div>
                </div>

                <div class="panel-body">
                    <div class="chart"
                         id="cpiChart"
                         data-type="line"
                         data-labels="{{$wasteIndex->pluck('p_name')}}"
                         data-datasets="[{{json_encode([
                                'label' => 'Waste Index', 'data' => $wasteIndex->pluck('value'),
                                'backgroundColor' => '#F0FFF3',
                                'borderColor' => '#8ed3d8'
                            ])}}]"
                         style="height: 200px"
                    ></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
@append