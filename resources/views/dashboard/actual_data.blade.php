<section class="card">
    <h3 class="card-title orange">Actual Data</h3>

    <div class="card-body">
    <table class="table table-bordered table-condensed">
        <tbody>
            <tr>
                <th class="w-1-3">Actual Cost</th>
                <td class="w-2-3">{{number_format($cost_info['to_date_cost'], 2)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Allowable Cost</th>
                <td class="w-2-3">{{number_format($cost_info['allowable_cost'], 2)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Variance</th>
                <td class="w-2-3 {{$cost_info['variance']>0? 'text-success' : 'text-danger'}}">
                    {{number_format($cost_info['variance'], 2)}}
                </td>
            </tr>
            <tr>
                <th class="w-1-3">CPI</th>
                <td class="w-2-3">
                    <div class="display-flex">
                        <span class="flex">{{number_format($cost_info['cpi'], 4)}}</span>
                        @if ($cost_info['cpi'] >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </div>
                </td>
                <td class="separator"></td>
                <th class="w-1-3">Waste Index</th>
                <td class="w-2-3">{{number_format($cost_info['pw_index'], 2)}}%</td>
                <td class="separator"></td>
                <th class="w-1-3">SPI</th>
                <td class="w-2-3">
                    <div class="display-flex">
                        <span class="flex">{{number_format($spi_trend->last(), 2)}}</span>
                        @if ($spi_trend->last() >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </div>

                </td>
            </tr>
            <tr class="high-risk-project">
                <th class="w-1-3">Highest Risk</th>
                <td class="w-2-3">{{$projectNames[$cost_info['highest_risk']['project_id']]}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Variance</th>
                <td class="w-2-3 {{$cost_info['highest_risk']->variance>0? 'text-success' : 'text-danger'}}">
                    {{number_format($cost_info['highest_risk']->variance, 2)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">CPI</th>
                <td class="w-2-3">
                    <div class="display-flex">
                        <span class="flex">{{number_format($cost_info['highest_risk']->cpi, 4)}}</span>
                        @if ($cost_info['highest_risk']->cpi >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </div>
                </td>
            </tr>
            <tr class="low-risk-project">
                <th class="w-1-3">Lowest Risk</th>
                <td class="w-2-3">{{$projectNames[$cost_info['lowest_risk']['project_id']]}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Variance</th>
                <td class="w-2-3 {{$cost_info['lowest_risk']->variance>0? 'text-success' : 'text-danger'}}">
                    {{number_format($cost_info['lowest_risk']->variance, 2)}}
                </td>
                <td class="separator"></td>
                <th class="w-1-3">CPI</th>
                <td class="w-2-3">
                    <div class="display-flex">
                        <span class="flex">{{number_format($cost_info['lowest_risk']->cpi, 4)}}</span>
                        @if ($cost_info['lowest_risk']->cpi >= 1)
                            <span class="text-success"><i class="fa fa-circle"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-circle"></i></span>
                        @endif
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    
</section>