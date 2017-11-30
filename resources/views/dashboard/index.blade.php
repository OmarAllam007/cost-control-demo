@extends('layouts.app')

@section('header')
    <h2><i class="fa fa-dashboard"></i> Dashboard</h2>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12">
            <section class="card">
                <h3 class="card-title">Contracts Information</h3>

                <div class="card-body row">
                    <div class="col-sm-4">
                        <dl>
                            <dt>Contracts Value</dt>
                            <dd>{{number_format($contracts_info['contracts_total'], 2)}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Change Orders Value</dt>
                            <dd>{{number_format($contracts_info['change_orders'], 2)}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Revised Contracts Value</dt>
                            <dd>{{number_format($contracts_info['revised'], 2)}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Profit</dt>
                            <dd>{{number_format($contracts_info['profit'], 2)}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Profitability</dt>
                            <dd>{{number_format($contracts_info['profitability'], 2)}}%</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Expected Finish Date</dt>
                            <dd>{{$contracts_info['finish_date']->format('d M Y') ?? ''}}</dd>
                        </dl>
                    </div>
                </div>

            </section>

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



            <section class="card">
                <h3 class="card-title">Cost Summary</h3>

                <div class="card-body">

                </div>
            </section>

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