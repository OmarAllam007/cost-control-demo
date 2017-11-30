<section class="card">
    <h3 class="card-title">Budget Data</h3>

    <div class="card-body display-flex">
        <article class="flex mr-10">
            <table class="table table-bordered mb-0">
                <tbody>
                <tr>
                    <th class="w-1-3">Revision Zero</th>
                    <td>{{number_format($budget_info['revision0']['budget_cost'], 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Direct Cost</th>
                    <td>{{number_format($budget_info['revision0']['direct_cost'], 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Indirect Cost</th>
                    <td>{{number_format($budget_info['revision0']['indirect_cost'], 2)}}</td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">Profit</th>
                    <td>{{number_format($budget_info['revision0']['profit'], 2)}}</td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">Profitability</th>
                    <td>{{number_format($budget_info['revision0']['profitability'], 2)}}</td>
                </tr>
                </tbody>
            </table>
        </article>

        <article class="flex">
            <table class="table table-bordered mb-0">
                <tbody>
                <tr>
                    <th class="w-1-3">Latest Revision</th>
                    <td>{{number_format($budget_info['revision1']['budget_cost'], 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Direct Cost</th>
                    <td>{{number_format($budget_info['revision1']['direct_cost'], 2)}}</td>
                </tr>
                <tr>
                    <th class="w-1-3">Indirect Cost</th>
                    <td>{{number_format($budget_info['revision1']['indirect_cost'], 2)}}</td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">Profit</th>
                    <td>{{number_format($budget_info['revision1']['profit'], 2)}}</td>
                </tr>
                <tr class="highlight">
                    <th class="w-1-3">Profitability</th>
                    <td>{{number_format($budget_info['revision1']['profitability'], 2)}}</td>
                </tr>
                </tbody>
            </table>
        </article>
    </div>
</section>