<h3 class="card-title  dark-green">Cost Summary</h3>
<section class="card-body">
    <table class="table table-bordered cost-summary-table">
        <thead>
        <tr>
            <th class="col-sm-2" rowspan="2"></th>
            <th class="cost-summary-group">Budget</th>
            <th colspan="3" class="cost-summary-group">Previous</th>
            <th colspan="3" class="cost-summary-group">To-Date</th>
            <th colspan="1" class="cost-summary-group">Remaining</th>
            <th colspan="3" class="cost-summary-group">At Completion</th>
        </tr>
        <tr>
            <th class="col-xs-1 cost-summary-header">Base Line</th>
            <th class="col-xs-1 cost-summary-header">Previous Cost</th>
            <th class="col-xs-1 cost-summary-header">Previous (EV) Allowable</th>
            <th class="col-xs-1 cost-summary-header">Previous Variance</th>
            <th class="col-xs-1 cost-summary-header">Todate Cost</th>
            <th class="col-xs-1 cost-summary-header">Allowable (EV) Cost</th>
            <th class="col-xs-1 cost-summary-header">Todate Cost Variance</th>
            <th class="col-xs-1 cost-summary-header">Remaining Cost</th>
            <th class="col-xs-1 cost-summary-header">at Completion Cost</th>
            <th class="col-xs-1 cost-summary-header">at Completion Cost Variance</th>
        </tr>

        </thead>
        <tbody>
        @php
            $typePreviousData = $previousData;
            $typeToDateData = $toDateData;
        @endphp

        @foreach($resourceTypes as $id => $value)
            <tr>
                <td class="cost-summary-side">{{$value}}</td>
                <td class="text-center">{{number_format($typeToDateData[$value]['budget_cost']??0,2) }}</td>
                <td class="text-center">{{number_format($typePreviousData[$value]['previous_cost']??0,2)}}</td>
                <td class="text-center">{{number_format($typePreviousData[$value]['previous_allowable']??0,2)}}</td>
                <td class="text-center">{{number_format($typePreviousData[$value]['previous_var']??0,2)}}</td>
                <td class="text-center">{{number_format($typeToDateData[$value]['to_date_cost']??0, 2)}}</td>
                <td class="text-center">{{number_format($typeToDateData[$value]['ev']??0,2)}}</td>
                <td style="@if(($typeToDateData['to_date_var'] ?? 0) < 0) color: red; @endif"
                    class="text-center">{{number_format($typeToDateData[$value]['to_date_var']??0,2)}}</td>
                <td class="text-center">{{number_format($typeToDateData[$value]['remaining_cost']??0,2)}}</td>
                <td class="text-center">{{number_format($typeToDateData[$value]['completion_cost']??0,2)}}</td>
                <td style=" @if(($typeToDateData['completion_cost_var']??0)<0) color: red; @endif"
                    class="text-center">{{number_format($typeToDateData[$value]['completion_cost_var']??0,2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background: #F0FFF3">
            <td class="cost-summary-side">Total</td>
            <td class="text-center">{{number_format($typeToDateData['Direct']['budget_cost'] + $typeToDateData['Indirect']['budget_cost']  ?? 0 ,2) }}</td>
            <td class="text-center">{{number_format($typePreviousData['Direct']['previous_cost'] ?? 0 + ($typePreviousData['Indirect']['previous_cost'] ?? 0) ,2) }}</td>
            <td class="text-center">{{number_format($typePreviousData['Direct']['previous_allowable'] ?? 0 + ( $typePreviousData['Indirect']['previous_allowable'] ?? 0) ,2) }}</td>
            <td class="text-center">{{number_format(($typePreviousData['Direct']['previous_var'] ?? 0) + ($typePreviousData['Indirect']['previous_var'] ??0),2) }}</td>
            <td class="text-center">{{number_format(($typeToDateData['Direct']['to_date_cost'] ?? 0) + ($typeToDateData['Indirect']['to_date_cost'] ?? 0),2) }}</td>
            <td class="text-center">{{number_format(($typeToDateData['Direct']['ev'] ) + ($typeToDateData['Indirect']['ev'] ?? 0) ,2) }}</td>
            <td class="text-center">{{number_format(($typeToDateData['Direct']['to_date_var'])  + ($typeToDateData['Indirect']['to_date_var']) ?? 0 ,2) }}</td>
            <td class="text-center">{{number_format(($typeToDateData['Direct']['remaining_cost'])  + ($typeToDateData['Indirect']['remaining_cost']) ?? 0 ,2) }}</td>
            <td class="text-center">{{number_format(($typeToDateData['Direct']['completion_cost'])  + ($typeToDateData['Indirect']['completion_cost']) ?? 0 ,2) }}</td>
            <td class="text-center">{{number_format(($typeToDateData['Direct']['completion_cost_var'] ) + ($typeToDateData['Indirect']['completion_cost_var']) ?? 0 ,2) }}</td>
        </tr>
        </tfoot>
    </table>
</section>
