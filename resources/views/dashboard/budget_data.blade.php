<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Budget Data</h4>

    <div class="card-body display-flex">
        <article class="flex mr-10">
            <table class="table table-bordered table-condensed mb-0">
                <tbody>
                <tr class="row-height">
                    <th class="w-30p">Revision Zero</th>
                    <td>{{number_format($budget_info['revision0']['budget_cost'], 2)}}
                </tr>
                <tr class="row-height">
                    <th class="w-30p">Direct Cost</th>
                    <td>{{number_format($budget_info['revision0']['direct_cost'], 2)}}</td>
                </tr>
                <tr class="row-height">
                    <th class="w-30p">EAC Contract Value</th>
                    <td>{{number_format($budget_info['revision0']['indirect_cost'], 2)}}</td>
                </tr>
                <tr class=" row-height">
                    <th class="w-30p">Planned Profit</th>
                    <td>{{number_format($budget_info['revision0']['profit'], 2)}}</td>
                </tr>
                <tr class=" row-height">
                    <th class="w-30p">Planned Profitability</th>
                    <td>{{number_format($budget_info['revision0']['profitability'], 2)}}</td>
                </tr>
                </tbody>
            </table>
        </article>

        <article class="flex">
            <table class="table table-bordered table-condensed mb-0">
                <tbody>
                <tr class=" row-height">
                    <th class="w-30p">Latest Revision</th>
                    <td>
                        <div class="display-flex">
                            <span class="flex">{{number_format($budget_info['revision1']['budget_cost'], 2)}}</span>
                            @if ($budget_info['revision1']['budget_cost'] >= $budget_info['revision0']['budget_cost'])
                                <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                            @else
                                <span class="text-danger"><i class="fa  fa-arrow--down"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr class=" row-height">
                    <th class="w-30p">Direct Cost</th>
                    <td>
                        <div class="display-flex">
                            <span class="flex">{{number_format($budget_info['revision1']['direct_cost'], 2)}}</span>
                            @if ($budget_info['revision1']['direct_cost'] >= $budget_info['revision0']['direct_cost'])
                                <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                            @else
                                <span class="text-danger"><i class="fa  fa-arrow-down"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr class=" row-height">
                    <th class="w-30p">Indirect Cost</th>
                    <td>
                        <div class="display-flex">
                            <span class="flex">{{number_format($budget_info['revision1']['indirect_cost'], 2)}}</span>
                            @if ($budget_info['revision1']['indirect_cost'] >= $budget_info['revision0']['indirect_cost'])
                                <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                            @else
                                <span class="text-danger"><i class="fa  fa-arrow-down"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>

                <tr class=" row-height">
                    <th class="w-30p">Profit</th>
                    <td>
                        <div class="display-flex">
                            <span class="flex">{{number_format($budget_info['revision1']['profit'], 2)}}</span>
                            @if ($budget_info['revision1']['profit'] >= $budget_info['revision0']['profit'])
                                <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                            @else
                                <span class="text-danger"><i class="fa  fa-arrow-down"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>

                <tr class=" row-height">
                    <th class="w-30p">Profitability</th>
                    <td>
                        <div class="display-flex">
                            <span class="flex">{{number_format($budget_info['revision1']['profitability'], 2)}}</span>
                            @if ($budget_info['revision1']['profitability'] >= $budget_info['revision0']['profitability'])
                                <span class="text-success"><i class="fa  fa-arrow-up"></i></span>
                            @else
                                <span class="text-danger"><i class="fa  fa-arrow-down"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </article>
    </div>
</section>