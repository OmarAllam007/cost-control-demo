@extends('layouts.app')

@section('header')
    <h2><i class="fa fa-dashboard"></i> Dashboard</h2>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12">
            @include('dashboard.filters')
        </div>
        <div class="col-md-12">
            @include('dashboard.project-info')
            @include('dashboard.budget_data')
            @include('dashboard.cost_summary')
        </div>
        <div class="col-md-6">
            @include('dashboard.actual_data')

            <section class="card-group-item">
                <h3 class="card-title dark-cyan card-group-item-heading">Waste Index Trend Analysis</h3>

                <div class="card-body">
                    <div class="chart"
                         id="wasteIndexTrendChart"
                         data-type="line"
                         data-labels="{{$waste_index_trend->keys()}}"
                         data-datasets="[{{ json_encode([
                                    'label' => 'Waste Index',
                                    'data' => $waste_index_trend->values(),
                                    'backgroundColor' => ['rgba(160, 240, 240, 0.3)']
                                ]) }}]"
                         style="height: 150px"></div>
                </div>
            </section>

            <section class="card-group-item">
                <h3 class="card-title dark-cyan card-group-item-heading" >SPI Trend Analysis</h3>

                <div class="card-body">
                    <div class="chart"
                         id="spiTrendChart"
                         data-type="line"
                         data-labels="{{$spi_trend->keys()}}"
                         data-datasets="[{{ json_encode([
                                    'label' => 'SPI Index',
                                    'data' => $spi_trend->values(),
                                    'backgroundColor' => ['rgba(100, 213, 202, 0.3)']
                                ]) }}]"
                         style="height: 150px"></div>
                </div>
            </section>

        </div>

        <div class="col-md-6">
            <section class="card-group-item">
                <h3 class="card-title dark-cyan card-group-item-heading">Revenue Statement</h3>

                <div class="card-body">
                    <div class="chart"
                         id="revenueTrendChart"
                         data-type="line"
                         data-labels="{{$actual_revenue_trend->keys()}}"
                         data-datasets="[{{ json_encode([
                                    'label' => 'Actual Revenue',
                                    'data' => $actual_revenue_trend->values(),
                                    'backgroundColor' => ['rgba(100, 213, 202, 0.3)']
                                ]) }}]"
                         style="height: 150px"></div>
                </div>
            </section>
            <section class="card-group-item">
                <h3 class="card-title dark-cyan card-group-item-heading">Productivity Index Trend Analysis</h3>

                <div class="card-body">
                    <div class="chart"
                         id="prodIndexTrendChart"
                         data-type="line"
                         data-labels="{{$pi_trend->keys()}}"
                         data-datasets="[{{ json_encode([
                                    'label' => 'Productivity Index',
                                    'data' => $pi_trend->values(),
                                    'backgroundColor' => ['rgba(100, 213, 202, 0.3)']
                                ]) }}]"
                         style="height: 150px"></div>
                </div>
            </section>
            <section class="card-group-item">
                <h3 class="card-title dark-cyan card-group-item-heading">CPI Trend Analysis</h3>

                <div class="card-body">
                    <div class="chart"
                         id="cpiTrendChart"
                         data-type="line"
                         data-labels="{{$cpi_trend->pluck('name')}}"
                         data-datasets="[{{ json_encode([
                                    'label' => 'CPI Index',
                                    'data' => $cpi_trend->pluck('cpi_index'),
                                    'backgroundColor' => ['rgba(160, 240, 240, 0.3)']
                                ]) }}]"
                         style="height: 150px"></div>
                </div>
            </section>
        </div>


            <div class="col-sm-6">
                <section class="card-group-item">
                    <h3 class="card-title dark-cyan card-group-item-heading">Cost Percentage</h3>

                    <div class="card-body">
                        <div class="chart"
                             id="costChart"
                             data-type="pie"
                             data-labels="{{json_encode(['Actual Cost', 'Remaining Cost'])}}"
                             data-datasets="[{{ json_encode([
                                    'label' => 'Cost Percentage',
                                    'data' => $cost_percentage_chart,
                                    'backgroundColor' => ['#64D5CA', '#88DBEF']
                                ]) }}]"
                             style="height: 200px"></div>
                    </div>
                </section>
            </div>

            <div class="col-sm-6">
                <section class="card-group-item">
                    <h3 class="card-title dark-cyan card-group-item-heading">Progress Percentage</h3>

                    <div class="card-body">
                        <div class="chart"
                             id="costChart"
                             data-type="horizontalBar"
                             data-labels="{{json_encode(['Actual', 'Planned'])}}"
                             data-datasets="[{{ json_encode([
                                    'label' => 'Progress',
                                    'data' => $cost_info['progress'],
                                    'backgroundColor' => ['#64D5CA', '#E3342F']
                                ]) }}]" style="height: 150px"></div>
                    </div>
                </section>
            </div>

    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
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
    </style>
@endsection