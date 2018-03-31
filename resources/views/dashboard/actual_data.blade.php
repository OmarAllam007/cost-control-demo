<section class="card-group-item">
    <h4 class="card-title dark-cyan card-group-item-heading">Actual Data</h4>

    <div class="card-body">
        <div class="display-flex">

            <div class="flex mr-10">
                <dl>
                    <dt>Actual Cost</dt>
                    <dd>{{number_format($cost_info['to_date_cost'], 2)}}</dd>
                </dl>
            </div>

            <div class="flex  mr-10">
                <dl>
                    <dt>Allowable Cost</dt>
                    <dd>{{number_format($cost_info['allowable_cost'], 2)}}</dd>
                </dl>
            </div>

            <div class="flex">
                <dl>
                    <dt>Variance</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['variance']>0? 'text-success' : 'text-danger'}}">
                            {{number_format($cost_info['variance'], 2)}}
                        </span>
                        <span class="{{$cost_info['eac_profit'] < 0? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="display-flex">

            <div class="flex mr-10">
                <dl>
                    <dt>CPI</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['cpi']>=1? 'text-success' : 'text-danger'}}">{{number_format($cost_info['cpi'], 4)}}</span>
                        @if ($cost_info['cpi'] >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </dd>
                </dl>
            </div>

            <div class="flex  mr-10">
                <dl>
                    <dt>MCI</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['pw_index'] > 4.75? 'text-danger' : 'text-success'}}">{{number_format($cost_info['pw_index'], 2)}}%</span>
                        <span class="{{$cost_info['pw_index'] > 4.75? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>

                    </dd>
                </dl>
            </div>

            <div class="flex">
                <dl>
                    <dt>SPI</dt>
                    <dd class="display-flex">
                        @php $spi_index = $spi_trend->get($period->name) @endphp
                        <span class="flex {{$spi_index<1?'text-danger' : 'text-success'}}">{{number_format($spi_index, 2)}}</span>
                        @if ($spi_trend->last() >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        <div class="display-flex">
            <div class="flex mr-10">
                <dl>
                    <dt>EAC Profit Amount</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['eac_profit'] < 0? 'text-danger' : 'text-success'}}">
                            {{number_format($cost_info['eac_profit'], 2)}}
                        </span>
                        <span class="{{$cost_info['eac_profit'] < 0? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>

            <div class="flex mr-10">
                <dl>
                    <dt>EAC Profitability</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['eac_profitability'] < 0? 'text-danger' : 'text-success'}}">
                            {{number_format($cost_info['eac_profitability'], 2)}}%
                        </span>
                        <span class="{{$cost_info['eac_profitability'] < 0? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>

            <div class="flex"></div>
        </div>

        <div class="display-flex mt-20" style="align-items: baseline">
            <div class="flex mr-10">
                <dl>
                    <dt>Highest Risk Project</dt>
                    <dd>
                        <span class="abbr" title="{{$cost_info['highest_risk']->name}}">
                            {{ str_limit($cost_info['highest_risk']->project_code, 25) }}
                        </span>
                    </dd>
                </dl>
            </div>
            <div class="flex mr-10">
                <dl>
                    <dt>Highest Risk Variance</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['highest_risk']->variance < 1? 'text-danger' : 'text-success'}}">{{number_format($cost_info['highest_risk']->variance, 2)}}</span>
                        <span class="{{$cost_info['highest_risk']->variance < 0? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>
            <div class="flex">
                <dl>
                    <dt>Highest Risk CPI</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['highest_risk']->cpi < 1? 'text-danger' : 'text-success'}}">{{number_format($cost_info['highest_risk']->cpi, 3)}}</span>
                        <span class="{{$cost_info['highest_risk']->cpi < 1? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="display-flex" style="align-items: baseline">
            <div class="flex mr-10">
                <dl>
                    <dt>Lowest Risk Project</dt>
                    <dd>
                        <span class="abbr" title="{{$cost_info['highest_risk']->name}}">
                            {{ str_limit($cost_info['lowest_risk']->project_code, 25) }}
                        </span>
                    </dd>
                </dl>
            </div>
            <div class="flex mr-10">
                <dl>
                    <dt>Lowest Risk Variance</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['lowest_risk']->variance < 0? 'text-danger' : 'text-success'}}">{{number_format($cost_info['lowest_risk']->variance, 2)}}</span>
                        <span class="{{$cost_info['lowest_risk']->variance < 0? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>
            <div class="flex">
                <dl>
                    <dt>Lowest Risk CPI</dt>
                    <dd class="display-flex">
                        <span class="flex {{$cost_info['lowest_risk']->cpi < 1? 'text-danger' : 'text-success'}}">{{number_format($cost_info['lowest_risk']->cpi, 3)}}</span>
                        <span class="{{$cost_info['lowest_risk']->cpi < 1? 'text-danger' : 'text-success'}}"><i class="fa fa-circle"></i></span>
                    </dd>
                </dl>
            </div>
        </div>

    </div>

</section>