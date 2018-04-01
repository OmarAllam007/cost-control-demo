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

                <dl>
                    <dt>CPI</dt>
                    <dd>
                        <div class="display-flex">
                            <span class="flex {{$costInfo['cpi'] > 1? 'text-success': 'text-danger'}}">{{number_format($costInfo['cpi'], 3)}}</span>
                            <span class="{{$costInfo['cpi'] > 1? 'text-success': 'text-danger'}}">
                                <i class="fa fa-circle"></i>
                            </span>
                        </div>

                    </dd>
                </dl>

                <dl>
                    <dt>Variance</dt>
                    <dd class="display-flex">
                        <span class="flex {{$costInfo['variance'] > 0 ? 'text-success' : 'text-danger'}}">{{number_format($costInfo['variance'], 2)}}</span>
                        <span class="{{$costInfo['variance'] > 0 ? 'text-success' : 'text-danger'}}"><i
                                    class="fa fa-circle"></i></span>
                    </dd>
                </dl>

                <dl>
                    <dt>Cost Progress</dt>
                    <dd>{{number_format($costInfo['cost_progress'], 2)}}%</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Time Elapsed</dt>
                    <dd>{{$period->time_elapsed ?: 0}} <small>(Days)</small></dd>
                </dl>
                <dl>
                    <dt>Time Remaining</dt>
                    <dd>{{$period->time_remaining ?: 0}} <small>(Days)</small></dd>
                </dl>
                <dl>
                    <dt>Expected Duration</dt>
                    <dd>{{$period->actual_duration ?: 0}} <small>(Days)</small></dd>
                </dl>

                <dl>
                    <dt>Duration Var</dt>
                    <dd class="display-flex">
                        <span class="flex {{$period->duration_variance > 0 ?'text-success':'text-danger'}}">
                            {{$period->duration_variance ?: 0}} <small>(Days)</small>
                        </span>

                        <span class="{{$period->duration_variance > 0? 'text-success' : 'text-danger'}}">
                            <i class="fa fa-circle"></i>
                        </span>
                    </dd>
                </dl>

                <dl>
                    <dt>Progress (Time)</dt>
                    <dd>{{$period->actual_progress}}%</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Actual Start Date</dt>
                    <dd>{{$project->actual_start_date? Carbon\Carbon::parse($project->actual_start_date)->format('d M Y') : ''}}</dd>
                </dl>
                <dl>
                    <dt>Expected Finish Date</dt>
                    <dd>{{$period->forecast_finish_date? Carbon\Carbon::parse($period->forecast_finish_date)->format('d M Y') : ''}}</dd>
                </dl>

                <dl>
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

                <dl>
                    <dt>MCI</dt>
                    <dd>
                        <div class="display-flex">
                            <span class="flex {{$costInfo['waste_index'] < 4.75 ? 'text-success' : 'text-danger'}}">
                                {{number_format($costInfo['waste_index'], 2)}}%
                            </span>

                            <span class="{{$costInfo['waste_index'] < 4.75 ? 'text-success' : 'text-danger'}}">
                                <i class="fa fa-circle"></i>
                            </span>
                        </div>
                    </dd>
                </dl>

                <dl>
                    <dt>Expected Profitability</dt>
                    <dd class="display-flex">
                        <span class="flex {{$period->eac_profitability_index > 0 ? 'text-success' : 'text-danger'}}">
                            {{number_format($period->eac_profitability_index, 2)}}%
                        </span>

                        <span class="{{$period->eac_profitability_index > 0 ? 'text-success' : 'text-danger'}}">
                            <i class="fa fa-circle"></i>
                        </span>
                    </dd>
                </dl>
            </article>
        </div>
    </section>

</section>