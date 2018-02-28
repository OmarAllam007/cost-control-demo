<section class="card-group-item">
    <h4 class="card-title dark-cyan card-group-item-heading">Actual Data</h4>

    <div class="card-body">
        <div class="row">

            <div class="col-sm-6">
                <dl>
                    <dt>Actual Cost</dt>
                    <dd>{{number_format($cost_info['to_date_cost'], 2)}}</dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>Allowable Cost</dt>
                    <dd>{{number_format($cost_info['allowable_cost'], 2)}}</dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>Variance</dt>
                    <dd class="{{$cost_info['variance']>0? 'text-success' : 'text-danger'}}">
                        {{number_format($cost_info['variance'], 2)}}
                    </dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>CPI</dt>
                    <dd class="display-flex">
                        <span class="flex">{{number_format($cost_info['cpi'], 4)}}</span>
                        @if ($cost_info['cpi'] >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>Material Consumption Index</dt>
                    <dd>{{number_format($cost_info['pw_index'], 2)}}%</dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>SPI</dt>
                    <dd class="display-flex">
                        <span class="flex">{{number_format($spi_trend->last(), 2)}}</span>
                        @if ($spi_trend->last() >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>EAC Profitability</dt>
                    <dd>

                    </dd>
                </dl>
            </div>

            <div class="col-sm-6">
                <dl>
                    <dt>EAC Profit Amount</dt>
                    <dd>

                    </dd>
                </dl>
            </div>
        </div>

    </div>

</section>