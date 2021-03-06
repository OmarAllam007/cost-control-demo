@php
    $totals = $topMaterialData->reduce(function($totals, $row){
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
    }, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0,'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

    $target = trim(slug($name)) . '-' . trim(slug($discipline)) . '-' . trim(slug($topMaterial));
@endphp
<tr class="top-material hidden {{trim(slug($name) . '-' . slug($discipline))}}">
    <td class="resource-cell right-border">
        <div class="display-flex">
            <strong class="flex"><a href="#" data-target=".{{$target}}"><i class="fa fa-plus-square-o"></i> {{$topMaterial}}</a></strong>
            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{json_encode([
                            'Top Material Group' => $topMaterial,
                            'Budget Cost' => number_format($totals['budget_cost']??0,2) ,
                            'Previous Cost' => number_format($totals['prev_cost'],2),
                            'Current Cost' => number_format($totals['curr_cost'],2),
                            'To Date Cost' => number_format($totals['to_date_cost'], 2),
                            'Allowable Cost' => number_format($totals['to_date_allowable'], 2),
                            'To Date Cost Var' => number_format($totals['to_date_cost_var'], 2),
                            'Remaining Cost' => number_format($totals['remaining_cost'], 2),
                            'At Completion Cost' => number_format($totals['at_completion_cost'], 2),
                            'At Completion Cost Var' => number_format($totals['cost_var'], 2),
                           ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
    </td>

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
@foreach($topMaterialData as $resource)
    @include('reports.cost-control.resource_code._resource', ['class' => 'top-material-resource', 'slug' => $target])
@endforeach