@php
$totals = $typeData->reduce(function ($totals, $disciplineData) {
    return $disciplineData->reduce(function ($totals, $topMaterialData) {
        return $topMaterialData->reduce(function ($totals, $row) {
            $totals['budget_cost'] += $row->budget_cost;
            $totals['prev_cost'] += $row->prev_cost;
            $totals['curr_cost'] += $row->curr_cost;
            $totals['to_date_cost'] += $row->to_date_cost;
            $totals['remaining_cost'] += $row->remaining_cost;
            $totals['at_completion_cost'] += $row->at_completion_cost;
            $totals['at_completion_var'] += $row->at_completion_var;
            $totals['cost_var'] += $row->cost_var;
            $totals['to_date_allowable'] += $row->to_date_allowable;
            $totals['to_date_cost_var'] = $totals['to_date_allowable'] - $totals['to_date_cost'];
            return $totals;
        }, $totals);
    }, $totals);
}, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0, 'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

@endphp
<tr class="resource-type">
    <td class="resource-cell right-border"><strong><a href="#" data-target=".{{slug($name)}}"><i class="fa fa-plus-square-o"></i> {{$name}} </a></strong></td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($totals['budget_cost'], 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($totals['prev_cost'], 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($totals['curr_cost'], 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">{{number_format($totals['to_date_cost'], 2)}}</td>
    <td class="number-cell">{{number_format($totals['to_date_allowable'], 2)}}</td>
    <td class="number-cell right-border {{$totals['to_date_cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($totals['to_date_cost_var'], 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($totals['remaining_cost'], 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">{{number_format($totals['at_completion_cost'], 2)}}</td>
    <td class="number-cell {{$totals['cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($totals['cost_var'], 2)}}</td>
    <td class="number-cell"></td>
</tr>

@foreach ($typeData as $discipline => $disciplineData)
    @include('reports.cost-control.resource_code._discipline', ['discipline' => $discipline ?: 'General'])
@endforeach