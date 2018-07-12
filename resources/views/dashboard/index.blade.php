@php
    $print = $print ?? request()->exists('print');
    $layout = ($print? 'print' : 'app');
@endphp
@extends("layouts.{$layout}")

@section('title', 'Dashboard')

@section('header')
    <div class="display-flex">
        <h2 class="flex"><i class="fa fa-dashboard no-print"></i> Dashboard</h2>

        @if (!$print)
            <div class="btn-toolbar">
                <a href="?refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Refresh</a>
                <a href="?print" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</a>
                <a href="{{url('/dashboard/send')}}" class="btn btn-primary btn-sm"><i class="fa fa-send"></i> Send Dashboard</a>
            </div>
        @endif
    </div>

@endsection

@section('body')
    <div class="row">
        <div class="col-md-12 col-xl-10 col-xl-offset-1">
            @if (!$print)
                @include('dashboard.filters')
            @endif

            @include('dashboard.project-info')
            @include('dashboard.budget_data')
            @include('dashboard.actual_data')
            @include('dashboard.cost_summary')


            <section class="row">
                <div class="col-md-{{$print? 12 : 6}}">
                    <article class="card-group-item">
                        <h4 class="card-title dark-cyan card-group-item-heading">Cost Performance Index (CPI) Trend</h4>

                        <div class="card-body">
                            <div class="chart"
                                 id="cpiTrendChart"
                                 data-type="line"
                                 data-formatter="number_3"
                                 data-labels="{{$cpi_trend->pluck('name')}}"
                                 data-datasets="[{{ json_encode([
                                    'label' => '',
                                    'data' =>$cpi_trend->pluck('cpi_index'),//$cpi_trend->pluck('cpi_index')
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                                 style="height: 150px"></div>
                        </div>
                    </article>
                </div>

                <div class="col-md-{{$print? 12 : 6}}">
                    <article class="card-group-item">
                        <h4 class="card-title dark-cyan card-group-item-heading">Schedule Performance Index (SPI) Trend</h4>

                        <div class="card-body">
                            <div class="chart"
                                 id="spiTrendChart"
                                 data-type="line"
                                 data-formatter="number_3"
                                 data-labels="{{$spi_trend->keys()}}"
                                 data-datasets="[{{ json_encode([
                                    'label' => '',
                                    'data' => $spi_trend->values(),//$spi_trend->values()
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                                 style="height: 150px"></div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="row">
                <div class="col-md-{{$print? 12 : 6}}">
                    <section class="card-group-item chart-style">
                        <h4 class="card-title card-group-item-heading">Material Consumption Index (MCI) Trend</h4>

                        <div class="card-body">
                            <div class="chart"
                                 id="wasteIndexTrendChart"
                                 data-type="line"
                                 data-formatter="number_3"
                                 data-labels="{{$waste_index_trend->keys()}}"
                                 data-datasets="[{{ json_encode([
                                    'label' => '',
                                    'data' => $waste_index_trend->values(),
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                                 style="height: 150px"></div>
                        </div>
                    </section>
                </div>
                <div class="col-md-{{$print? 12 : 6}}">
                    <section class="card-group-item">
                        <h4 class="card-title dark-cyan card-group-item-heading">Productivity Index (PI) Trend</h4>

                        <div class="card-body">
                            <div class="chart"
                                 id="prodIndexTrendChart"
                                 data-type="line"
                                 data-formatter="number_3"
                                 data-labels="{{$pi_trend->keys()}}"
                                 data-datasets="[{{ json_encode([
                                    'label' => '',
                                    'data' => $pi_trend->values(),
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                                 style="height: 150px"></div>
                        </div>
                    </section>
                </div>
            </section>

            <div class="row">
                <div class="col-sm-{{$print? 12 : 6}}">
                    <section class="card-group-item">
                        <h4 class="card-title dark-cyan card-group-item-heading">Cost Performance</h4>

                        <div class="card-body pie-chart">
                            <div class="chart"
                                 id="costChart"
                                 data-type="pie"
                                 data-formatter="percent"
                                 data-animate="false"
                                 data-labels="{{json_encode(['Actual Cost', 'Remaining Cost'])}}"
                                 data-datasets="[{{ json_encode([
                                    'label' => 'Cost Percentage',
                                    'data' => $cost_percentage_chart,
                                    'backgroundColor' => [ 'rgba(38,89,137,.6)', 'rgba(0, 32, 96, 0.9)']
                                ]) }}]"
                                 style="height: 200px"></div>
                        </div>
                    </section>
                </div>

                <div class="col-sm-{{$print? 12 : 6}}">
                    <section class="card-group-item">
                        <h4 class="card-title dark-cyan card-group-item-heading">Physical Performance</h4>

                        <div class="card-body">
                            <div class="chart"
                                 id="costChart"
                                 data-type="horizontalBar"
                                 data-formatter="percent"
                                 data-labels="{{json_encode(['Actual', 'Planned'])}}"
                                 data-datasets="[{{ json_encode([
                                    'label' => '',
                                    'data' =>$cost_info['progress'],//
                                    'backgroundColor' => ['rgba(38,89,137,.6)', 'rgba(214,117,53,.6)'],
                                ]) }}]"


                                 style="height: 200px"></div>
                        </div>
                    </section>
                </div>
            </div>


            <article class="card-group-item">
                <h4 class="card-group-item-heading">Revenue Statement</h4>

                <div class="row">
                    <div class="col-md-12">
                        <div class="chart"
                             id="wasteIndexChart"
                             data-type="horizontalBar"
                             data-labels="{{collect(['Planned Value (PV)', 'Earned Value (EV)', 'Actual Invoice Value'])}}"
                             data-datasets="[{{json_encode([
                                'label' => '',
                                'data' => $revenue_statement,
                                'backgroundColor' => ['rgba(65,108,182,0.6)', 'rgba(104,160,72,0.6)', "rgba(214,117,53,.7)"],
                                   'borderColor' => '#5B9BD5',
                                'datalabels' => ['align' => 'right']
                            ])}}]"
                             style="height: 200px"></div>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
    <script>
        $(function () {
            $('.abbr').tooltip();
        })
    </script>
@endsection

@section('css')
    <style>
        .card .table > tbody > tr.low-risk-project > th, .card .table > tbody > tr.low-risk-project > td {
            background-color: #dfd;
        }

        .card .table > tbody > tr.high-risk-project > th, .card .table > tbody > tr.high-risk-project > td {
            background-color: #fdd;
        }

        .card .table > tbody > tr.low-risk-project > th.separator, .card .table > tbody > tr.low-risk-project > td.separator {
            background-color: transparent;
        }

        .card .table > tbody > tr.high-risk-project > th.separator, .card .table > tbody > tr.high-risk-project > td.separator {
            background-color: transparent;
        }

        .card-group-item .pie-chart canvas {
            max-width: initial;
        }
    </style>
@endsection