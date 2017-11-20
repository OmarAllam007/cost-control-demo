<h4 class="section-header">Budget Information</h4>

<table class="table table-condensed budget-info">
    <thead>
    <tr>
        <th class="col-xs-2"></th>
        <th class="revision-0 col-xs-5">Revision Zero</th>
        <th class="revision-1 col-xs-5">Last Revision</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th>Total Budget Cost</th>
        <td class="revision-0">{{number_format($budgetInfo['revision0']['budget_cost'], 2)}}</td>
        <td class="revision-1">
            <div class="display-flex">
                <span class="flex">{{number_format($budgetInfo['revision1']['budget_cost'], 2)}}</span>

                @if ($budgetInfo['revision1']['budget_cost'] > $budgetInfo['revision0']['budget_cost'])
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @elseif($budgetInfo['revision1']['budget_cost'] < $budgetInfo['revision0']['budget_cost'])
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @else
                    <span><strong>&mdash;</strong></span>
                @endif
            </div>
        </td>
    </tr>
    <tr>
        <th>Direct Cost</th>
        <td class="revision-0">{{number_format($budgetInfo['revision0']['direct_cost'], 2)}}</td>
        <td class="revision-1">
            <div class="display-flex">
                <span class="flex">{{number_format($budgetInfo['revision1']['direct_cost'], 2)}}</span>

                @if ($budgetInfo['revision1']['direct_cost'] > $budgetInfo['revision0']['direct_cost'])
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @elseif($budgetInfo['revision1']['direct_cost'] < $budgetInfo['revision0']['direct_cost'])
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @else
                    <span><strong>&mdash;</strong></span>
                @endif
            </div>
        </td>
    </tr>
    <tr>
        <th>Indirect Cost</th>
        <td class="revision-0">{{number_format($budgetInfo['revision0']['indirect_cost'], 2)}}</td>
        <td class="revision-1">
            <div class="display-flex">
                <span class="flex">{{number_format($budgetInfo['revision1']['indirect_cost'], 2)}}</span>

                @if ($budgetInfo['revision1']['indirect_cost'] > $budgetInfo['revision0']['indirect_cost'])
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @elseif($budgetInfo['revision1']['indirect_cost'] < $budgetInfo['revision0']['indirect_cost'])
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @else
                    <span><strong>&mdash;</strong></span>
                @endif
            </div>
        </td>
    </tr>
    <tr>
        <th>Management Reserve</th>
        <td class="revision-0">{{number_format($budgetInfo['revision0']['management_reserve'], 2)}}</td>
        <td class="revision-1">
            <div class="display-flex">
                <span class="flex">{{number_format($budgetInfo['revision1']['management_reserve'], 2)}}</span>

                @if ($budgetInfo['revision1']['management_reserve'] > $budgetInfo['revision0']['management_reserve'])
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @elseif($budgetInfo['revision1']['management_reserve'] < $budgetInfo['revision0']['management_reserve'])
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @else
                    <span><strong>&mdash;</strong></span>
                @endif
            </div>
        </td>
    </tr>
    <tr>
        <th>Profit</th>
        <td class="revision-0">{{number_format($budgetInfo['revision0']['profit'], 2)}}</td>
        <td class="revision-1">
            <div class="display-flex">
                <span class="flex">{{number_format($budgetInfo['revision1']['profit'], 2)}}</span>

                @if ($budgetInfo['revision1']['profit'] > $budgetInfo['revision0']['profit'])
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @elseif($budgetInfo['revision1']['profit'] < $budgetInfo['revision0']['profit'])
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @else
                    <span><strong>&mdash;</strong></span>
                @endif
            </div>
        </td>
    </tr>
    <tr>
        <th>Profitability</th>
        <td class="revision-0">{{number_format($budgetInfo['revision0']['profitability_index'], 2)}}%</td>
        <td class="revision-1">
            <div class="display-flex">
                <span class="flex">{{number_format($budgetInfo['revision1']['profitability_index'], 2)}}%</span>

                @if ($budgetInfo['revision1']['profitability_index'] > $budgetInfo['revision0']['profitability_index'])
                    <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                @elseif($budgetInfo['revision1']['profitability_index'] < $budgetInfo['revision0']['profitability_index'])
                    <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                @else
                    <span><strong>&mdash;</strong></span>
                @endif

            </div>
        </td>
    </tr>
    </tbody>
</table>