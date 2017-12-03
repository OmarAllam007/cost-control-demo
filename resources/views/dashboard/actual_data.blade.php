<section class="card">
    <h3 class="card-title">Actual Data</h3>

    <div class="card-body">
    <table class="table table-bordered table-condensed">
        <tbody>
            <tr>
                <th class="w-1-3">Actual Cost</th>
                <td class="w-2-3">{{number_format($cost_info['to_date_cost'], 2)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Allowable Cost</th>
                <td class="w-2-3">{{number_format($cost_info['allowable_cost'])}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Variance</th>
                <td class="w-2-3">{{number_format($cost_info['variance'], 2)}}</td>
            </tr>
            <tr>
                <th class="w-1-3">CPI</th>
                <td class="w-2-3">{{number_format($cost_info['cpi'], 4)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Waste Index</th>
                <td class="w-2-3"></td>                
                <td class="separator"></td>
                <th class="w-1-3">SPI</th>
                <td class="w-2-3"></td>
            </tr>
            <tr class="highlight">
                <th class="w-1-3">Highest Risk</th>
                <td class="w-2-3">{{$projectNames[$cost_info['highest_risk']['project_id']]}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Variance</th>
                <td class="w-2-3">{{number_format($cost_info['highest_risk']->variance, 2)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">CPI</th>
                <td class="w-2-3">{{number_format($cost_info['highest_risk']->cpi, 4)}}</td>
            </tr>
            <tr>
                <th class="w-1-3">Lowest Risk</th>
                <td class="w-2-3">{{$projectNames[$cost_info['lowest_risk']['project_id']]}}</td>
                <td class="separator"></td>
                <th class="w-1-3">Variance</th>
                <td class="w-2-3">{{number_format($cost_info['lowest_risk']->variance, 2)}}</td>
                <td class="separator"></td>
                <th class="w-1-3">CPI</th>
                <td class="w-2-3">{{number_format($cost_info['lowest_risk']->cpi, 4)}}</td>
            </tr>
        </tbody>
    </table>
    </div>
    
</section>