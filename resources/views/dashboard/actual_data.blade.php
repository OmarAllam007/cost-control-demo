<section class="card">
    <h3 class="card-title">Actual Data</h3>

    <div class="card-body display-flex">
        <article class="flex mr-10">
            <table class="table table-bordered mb-0">
                <tbody>
                <tr>
                    <th class="w-1-3">Actual Cost</th>
                    <td>{{number_format($cost_info['to_date_cost'], 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">CPI</th>
                    <td>{{number_format($cost_info['cpi'], 4)}}</td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">Highest Risk Project</th>
                    <td>{{$projectNames[$cost_info['highest_risk']['project_id']]}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Lowest Risk Project</th>
                    <td>{{$projectNames[$cost_info['lowest_risk']['project_id']]}}</td>
                </tr>
                </tbody>
            </table>
        </article>

        <article class="flex mr-10">
            <table class="table table-bordered mb-0">
                <tbody>
                <tr>
                    <th class="w-1-3">Allowable Cost</th>
                    <td>{{number_format($cost_info['allowable_cost'])}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Waste Index</th>
                    <td></td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">Variance</th>
                    <td>{{number_format($cost_info['highest_risk']->variance, 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Variance</th>
                    <td>{{number_format($cost_info['lowest_risk']->variance, 2)}}</td>
                </tr>
                </tbody>
            </table>
        </article>

        <article class="flex">
            <table class="table table-bordered mb-0">
                <tbody>
                <tr>
                    <th class="w-1-3">Variance</th>
                    <td>{{number_format($cost_info['variance'], 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">SPI</th>
                    <td></td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">CPI</th>
                    <td>{{number_format($cost_info['highest_risk']->cpi, 4)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">CPI</th>
                    <td>{{number_format($cost_info['lowest_risk']->cpi, 4)}}</td>
                </tr>
                </tbody>
            </table>
        </article>
    </div>
</section>