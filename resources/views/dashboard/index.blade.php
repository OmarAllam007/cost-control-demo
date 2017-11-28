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
            <section class="card">
                <h3 class="card-title">Budget Data</h3>

                <div class="card-body display-flex">
                    <article class="flex mr-10">
                        <table class="table table-bordered mb-0">
                            <tbody>
                            <tr>
                                <th class="w-1-3">Revision Zero</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">Direct Cost</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">Indirect Cost</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">Profit</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">Profitability</th>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </article>

                    <article class="flex">
                        <table class="table table-bordered mb-0">
                            <tbody>
                            <tr>
                                <th class="w-1-3">Latest Revision</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">Direct Cost</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">Indirect Cost</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">Profit</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">Profitability</th>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </article>
                </div>
            </section>

            <section class="card">
                <h3 class="card-title">Actual Data</h3>


                <div class="card-body display-flex">
                    <article class="flex mr-10">
                        <table class="table table-bordered mb-0">
                            <tbody>
                            <tr>
                                <th class="w-1-3">Actual Cost</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">CPI</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">Highest Risk Project</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">Lowest Risk Project</th>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </article>

                    <article class="flex mr-10">
                        <table class="table table-bordered mb-0">
                            <tbody>
                            <tr>
                                <th class="w-1-3">Allowable Cost</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">SPI</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">Variance</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">Variance</th>
                                <td></td>
                            </tr>

                            </tbody>
                        </table>
                    </article>

                    <article class="flex">
                        <table class="table table-bordered mb-0">
                            <tbody>
                            <tr>
                                <th class="w-1-3">Variance</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">SPI</th>
                                <td></td>
                            </tr>
                            <tr class="highlight">
                                <th class="w-1-3">CPI</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="w-1-3">CPI</th>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </article>
                </div>
            </section>

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