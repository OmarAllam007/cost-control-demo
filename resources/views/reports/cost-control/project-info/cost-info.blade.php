<h4 class="section-header">Actual Data</h4>

<div class="row">
    <article class="col-xs-4">
        <dl class="mb-1 no-shadow">
            <dt>Actual Cost</dt>
            <dd>{{number_format($costInfo['actual_cost'], 2)}}</dd>
        </dl>
        <dl class="mb-1 no-shadow">
            <dt>Allowable Cost</dt>
            <dd>{{number_format($costInfo['allowable_cost'], 2)}}</dd>
        </dl>
        <dl>
            <dt>Variance</dt>
            <dd>{{number_format($costInfo['variance'], 2)}}</dd>
        </dl>

        <dl>
            <dt>CPI</dt>
            <dd>
                <div class="display-flex">
                <span class="flex">{{number_format($costInfo['cpi'], 3)}}</span>
                <span class="{{$costInfo['cpi'] > 1? 'text-success': 'text-warning'}}"><i class="fa fa-circle"></i></span>
                </div>

            </dd>
        </dl>

        <dl>
            <dt>%age Progress (Cost)</dt>
            <dd>{{number_format($costInfo['cost_progress'], 2)}}%</dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl class="mb-1 no-shadow">
            <dt>Time Elapsed</dt>
            <dd>{{$period->time_elapsed}}</dd>
        </dl>
        <dl class="mb-1 no-shadow">
            <dt>Time Remaining</dt>
            <dd>{{$period->time_remaining}}</dd>
        </dl>
        <dl>
            <dt>Expected Duration</dt>
            <dd>{{$period->expecetd_duraion}}</dd>
        </dl>

        <dl>
            <dt>Duration Var (Days)</dt>
            <dd><span class="{{$period->duration_variance < 0 ?'':'text-danger'}}">{{$period->duration_variance}}</span></dd>
        </dl>
    </article>

    <article class="col-xs-4">
        <dl class="mb-1 no-shadow">
            <dt>Actual Start Date</dt>
            <dd>{{$project->actual_start_date}}</dd>
        </dl>
        <dl>
            <dt>Expected Finish Date</dt>
            <dd>{{$project->expected_finish_date}}</dd>
        </dl>

        <dl>
            <dt>SPI</dt>
            <dd>{{$period->spi_index}}</dd>
        </dl>

        <dl>
            <dt>%age Progress (Time)</dt>
            <dd>{{$period->actual_progress}}%</dd>
        </dl>

        <dl>
            <dt>Productivity Index</dt>
            <dd>{{$costInfo['productivity_index']}}%</dd>
        </dl>
    </article>
</div>