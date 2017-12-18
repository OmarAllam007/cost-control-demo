@extends('layouts.app')

@section('header')
    <h2><i class="fa fa-dashboard"></i> Dashboard</h2>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12">
            @include('dashboard.project-info')

            {{--
            <section class="card">
                 <h3 class="card-title">Finish Dates</h3>

                 <div class="card-body">
                     <div class="chart" data-type="scatter"
                          style="min-height: 250px"
                          data-datasets="{&quot;data&quot;[]}"
                          data-labels="{{$finish_dates->pluck('title')}}"></div>
                 </div>
             </section>
            --}}
            @include('dashboard.budget_data')
            @include('dashboard.actual_data')

            @include('dashboard.cost_summary')

            <section class="card">
                <h3 class="card-title">CPI Trend Analysis</h3>

                <div class="card-body">

                </div>
            </section>

            <section class="card">
                <h3 class="card-title">SPI Trend Analysis</h3>

                <div class="card-body">

                </div>
            </section>

            <section class="card">
                <h3 class="card-title">Waste Index Trend Analysis</h3>

                <div class="card-body">

                </div>
            </section>

            <section class="card">
                <h3 class="card-title">Productivity Index Trend Analysis</h3>

                <div class="card-body">

                </div>
            </section>

            <section class="card">
                <h3 class="card-title">Labour Trend Analysis (Man/Month)</h3>

                <div class="card-body">

                </div>
            </section>

            <section class="card">
                <h3 class="card-title">Actual Revenue</h3>

                <div class="card-body">

                </div>
            </section>

            <div class="row">
                <div class="col-sm-6">
                    <section class="card">
                        <h3 class="card-title">Cost Percentage</h3>

                        <div class="card-body">
                            <div class="chart"
                                id="costChart"
                                data-type="pie"
                                data-labels="{{json_encode(['Actual Cost', 'Remaining Cost'])}}"
                                data-datasets="[{{ json_encode([
                                    'label' => 'Cost Percentage', 
                                    'data' => $cost_percentage_chart,
                                    'backgroundColor' => ['#64D5CA', '#E3342F']
                                ]) }}]"
                                style="height: 200px"></div>
                        </div>
                    </section>
                </div>

                <div class="col-sm-6">
                    <section class="card">
                        <h3 class="card-title">Progress Percentage</h3>

                        <div class="card-body">

                        </div>
                    </section>
                </div>
            </div>

            <section class="card">
                <h3 class="card-title">Actual Revenue</h3>

                <div class="card-body">

                </div>
            </section>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
@endsection