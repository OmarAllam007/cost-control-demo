<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Actual Data</h4>
    <section class="card-body">

    <div class="row">
        <article class="col-xs-4">
            <dl>
                <dt>Actual Cost</dt>
                <dd>{{number_format($costInfo['actual_cost'], 2)}}</dd>
            </dl>
            <dl>
                <dt>Allowable Cost</dt>
                <dd>{{number_format($costInfo['allowable_cost'], 2)}}</dd>
            </dl>

            <dl class="dl-margin">
                <dt>CPI</dt>
                <dd>
                    <div class="display-flex">
                        <span class="flex {{$costInfo['cpi'] > 1? 'text-success': 'text-warning'}}">{{number_format($costInfo['cpi'], 3)}}</span>
                        <span class="{{$costInfo['cpi'] > 1? 'text-success': 'text-warning'}}"><i
                                    class="fa fa-circle"></i></span>
                    </div>

                </dd>
            </dl>

            <dl class="dl-margin">
                <dt>Variance</dt>
                <dd class="display-flex">
                    <span class="flex {{$costInfo['variance'] > 0 ? 'text-success' : 'text-danger'}}">{{number_format($costInfo['variance'], 2)}}</span>
                    <span class="{{$costInfo['variance'] > 0 ? 'text-success' : 'text-danger'}}"><i
                                class="fa fa-circle"></i></span>
                </dd>
            </dl>

            <dl>
                <dt>Actual Cost Percentage</dt>
                <dd>{{number_format($costInfo['cost_progress'], 2)}}%</dd>
            </dl>
        </article>

        <article class="col-xs-4">
            <dl>
                <dt>Time Elapsed</dt>
                <dd>{{$period->time_elapsed}}</dd>
            </dl>
            <dl>
                <dt>Time Remaining</dt>
                <dd>{{$period->time_remaining}}</dd>
            </dl>
            <dl>
                <dt>Expected Duration</dt>
                <dd>{{$period->expected_duration}}</dd>
            </dl>

            <dl class="dl-margin">
                <dt>Duration Var (Days)</dt>
                <dd>
                    <span class="{{$period->duration_variance < 0 ?'':'text-danger'}}">{{$period->duration_variance}}</span>
                </dd>
            </dl>

            <dl>
                <dt>%age Progress (Time)</dt>
                <dd>{{$period->actual_progress}}%</dd>
            </dl>
        </article>

        <article class="col-xs-4">
            <dl class="mb-1">
                <dt>Actual Start Date</dt>
                <dd>{{Carbon\Carbon::parse($project->actual_start_date)->format('d M Y')}}</dd>
            </dl>
            <dl>
                <dt>Expected Finish Date</dt>
                <dd>{{Carbon\Carbon::parse($period->forecast_finish_date)->format('d M Y')}}</dd>
            </dl>

            <dl class="dl-margin">
                <dt>SPI</dt>
                <dd>
                    <div class="display-flex">
                        <span class="display-flex {{$period->spi_index >= 1 ? 'text-success' : 'text-danger'}}">{{number_format($period->spi_index, 3)}}</span>
                        <span class="{{$period->spi_index >= 1 ? 'text-success' : 'text-danger'}}">
                            <i class="fa fa-circle"></i>
                        </span>
                    </div>
                </dd>
            </dl>

            <dl class="dl-margin">
                <dt>Material Consumption Index</dt>
                <dd>
                    <div class="display-flex">
                        <span class="display-flex {{$costInfo['waste_index'] < 4.475 ? 'text-success' : 'text-danger'}}">{{number_format($costInfo['waste_index'], 2)}}</span>
                        <span class="{{$costInfo['waste_index'] < 4.475 ? 'text-success' : 'text-danger'}}">
                            <i class="fa fa-circle"></i>
                        </span>
                    </div>
                </dd>
            </dl>

            <dl>
                <dt>Expected EAC Profit Profitability</dt>
                <dd class="{{$period->eac_profitability_index > 0 ? 'text-success' : 'text-danger'}}">{{number_format($period->eac_profitability_index, 2)}}%</dd>
            </dl>
        </article>
    </div>
    </section>

</section>