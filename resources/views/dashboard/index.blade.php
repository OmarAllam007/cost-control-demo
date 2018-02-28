@extends('layouts.app')

@section('header')
    <h2><i class="fa fa-dashboard"></i> Dashboard</h2>
@endsection

@section('body')
    <div class="col-xl-10 col-xl-offset-1">

        @include('dashboard.filters')

        @include('dashboard.project-info')
        @include('dashboard.budget_data')
        @include('dashboard.cost_summary')

        @include('dashboard.actual_data')


        <section class="card-group-item">
            <h4 class="card-title dark-cyan card-group-item-heading">CPI Trend Analysis</h4>

            <div class="card-body">
                <div class="chart"
                     id="cpiTrendChart"
                     data-type="line"
                     data-labels="{{$cpi_trend->pluck('name')}}"
                     data-datasets="[{{ json_encode([
                                    'label' => 'CPI Index',
                                    'data' =>$cpi_trend->pluck('cpi_index'),//$cpi_trend->pluck('cpi_index')
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                     style="height: 150px"></div>
            </div>
        </section>

        <section class="card-group-item">
            <h4 class="card-title dark-cyan card-group-item-heading">SPI Trend Analysis</h4>

            <div class="card-body">
                <div class="chart"
                     id="spiTrendChart"
                     data-type="line"
                     data-labels="{{$spi_trend->keys()}}"
                     data-datasets="[{{ json_encode([
                                    'label' => 'SPI Index',
                                    'data' => $spi_trend->values(),//$spi_trend->values()
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                     style="height: 150px"></div>
            </div>
        </section>

        <section class="card-group-item">
            <h4 class="card-title dark-cyan card-group-item-heading">Revenue Statement</h4>

            <div class="card-body">
                <div class="chart"
                     id="revenueTrendChart"
                     data-type="line"
                     data-labels="{{$actual_revenue_trend->keys()}}"
                     data-datasets="[{{ json_encode([
                                    'label' => 'Actual Revenue',
                                    'data' =>$actual_revenue_trend->values(),//)
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                     style="height: 150px"></div>
            </div>
        </section>

        <section class="card-group-item chart-style">
            <h4 class="card-title card-group-item-heading">Waste Index Trend Analysis</h4>

            <div class="card-body">
                <div class="chart"
                     id="wasteIndexTrendChart"
                     data-type="line"
                     data-labels="{{$waste_index_trend->keys()}}"
                     data-datasets="[{{ json_encode([
                                    'label' => 'Waste Index',
                                    'data' => $waste_index_trend->values(),// $waste_index_trend->values(),
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                     style="height: 150px"></div>
            </div>
        </section>

        <section class="card-group-item">
            <h4 class="card-title dark-cyan card-group-item-heading">Productivity Index Trend Analysis</h4>

            <div class="card-body">
                <div class="chart"
                     id="prodIndexTrendChart"
                     data-type="line"
                     data-labels="{{$pi_trend->keys()}}"
                     data-datasets="[{{ json_encode([
                                    'label' => 'Productivity Index',
                                    'data' => $pi_trend->values(),//$pi_trend->values()
                                    'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                    'borderColor' => 'rgba(0, 32, 96, 0.9)'
                                ]) }}]"
                     style="height: 150px"></div>
            </div>
        </section>

        <div class="row">
            <div class="col-sm-6">
                <section class="card-group-item">
                    <h4 class="card-title dark-cyan card-group-item-heading">Cost Percentage</h4>

                    <div class="card-body">
                        <div class="chart"
                             id="costChart"
                             data-type="pie"
                             data-labels="{{json_encode(['Actual Cost', 'Remaining Cost'])}}"
                             data-datasets="[{{ json_encode([
                                    'label' => 'Cost Percentage',
                                    'data' => $cost_percentage_chart,//$cost_percentage_chart
                                    'backgroundColor' => [ 'rgba(217, 225, 242, 0.6)', 'rgba(0, 32, 96, 0.9)']
                                ]) }}]"
                             style="height: 200px"></div>
                    </div>
                </section>
            </div>

            <div class="col-sm-6">
                <section class="card-group-item">
                    <h4 class="card-title dark-cyan card-group-item-heading">Progress Percentage</h4>

                    <div class="card-body">
                        <div class="chart"
                             id="costChart"
                             data-type="horizontalBar"
                             data-labels="{{json_encode(['Actual', 'Planned'])}}"
                             data-datasets="[{{ json_encode([
                                    'label' => 'Progress',
                                    'data' =>$cost_info['progress'],//
                                    'backgroundColor' => ['rgba(38,89,137,.6)', 'rgba(214,117,53,.6)'],
                                ]) }}]"


                             style="height: 200px"></div>
                    </div>
                </section>
            </div>
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