@php $to_date_var = $resource->to_date_allowable - $resource->to_date_cost; @endphp
<tr class="{{$class}} {{$slug}} hidden">
    <td class="resource-cell right-border">
        <div class="display-flex">
            <span class="flex">
                <i class="fa fa-caret-right"></i> {{$resource->resource_name}}
            </span>

            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{json_encode([
                            'Resource' => $resource->resource_name,
                            'Budget U.Price' => number_format($resource->budget_qty? $resource->budget_cost / $resource->budget_qty : 0, 2),
                            'Budget Qty' => number_format($resource->budget_qty, 2),
                            'Budget Cost' => number_format($resource->budget_cost, 2),
                            'Previous U.Price' => number_format($resource->prev_qty? $resource->prev_cost / $resource->prev_qty : 0, 2),
                            'Previous Qty' => number_format($resource->prev_qty, 2),
                            'Previous Cost' => number_format($resource->prev_cost, 2),
                            'Current U.Price' => number_format($resource->curr_qty? $resource->curr_cost / $resource->curr_qty : 0, 2),
                            'Current Qty' => number_format($resource->curr_qty, 2),
                            'Current Cost' => number_format($resource->curr_cost, 2),
                            'To Date U.Price' => number_format($resource->to_date_qty? $resource->to_date_cost / $resource->to_date_qty : 0, 2),
                            'To Date Qty' => number_format($resource->to_date_qty, 2),
                            'To Date Allowable Qty' => number_format($resource->to_date_allowable_qty, 2),
                            'To Date Qty Var' => number_format($resource->to_date_allowable_qty - $resource->to_date_qty, 2),
                            'To Date Cost' => number_format($resource->to_date_cost, 2),
                            'To Date Allowable Cost' => number_format($resource->to_date_allowable, 2),
                            'To Date Cost Var' => number_format($to_date_var, 2),
                            'Remaining U.Price' => number_format($resource->remaining_qty? $resource->remaining_cost / $resource->remaining_qty : 0, 2),
                            'Remaining Qty' => number_format($resource->remaining_qty, 2),
                            'Remaining Cost' => number_format($resource->remaining_cost, 2),
                            'At Completion U.Price' => number_format($resource->at_completion_qty? $resource->at_completion_cost / $resource->at_completion_qty : 0, 2),
                            'At Completion Qty' => number_format($resource->at_completion_qty, 2),
                            'At Completion Qty Var' => number_format($resource->qty_var, 2),
                            'At Completion Cost' => number_format($resource->at_completion_cost, 2),
                            'At Completion Cost Var' => number_format($resource->cost_var, 2),
                           ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>

    </td>

    <td class="number-cell">{{number_format($resource->budget_qty? $resource->budget_cost / $resource->budget_qty : 0, 2)}}</td>
    <td class="number-cell">{{number_format($resource->budget_qty, 2)}}</td>
    <td class="number-cell right-border">{{number_format($resource->budget_cost, 2)}}</td>

    <td class="number-cell">{{number_format($resource->prev_qty? $resource->prev_cost / $resource->prev_qty : 0, 2)}}</td>
    <td class="number-cell">{{number_format($resource->prev_qty, 2)}}</td>
    <td class="number-cell right-border">{{number_format($resource->prev_cost, 2)}}</td>

    <td class="number-cell">{{number_format($resource->curr_qty? $resource->curr_cost / $resource->curr_qty : 0, 2)}}</td>
    <td class="number-cell">{{number_format($resource->curr_qty, 2)}}</td>
    <td class="number-cell right-border">{{number_format($resource->curr_cost, 2)}}</td>


    <td class="number-cell">{{number_format($resource->to_date_qty? $resource->to_date_cost / $resource->to_date_qty : 0, 2)}}</td>
    <td class="number-cell">{{number_format($resource->to_date_qty, 2)}}</td>
    <td class="number-cell">{{number_format($resource->to_date_allowable_qty, 2)}}</td>
    <td class="number-cell">{{number_format($resource->to_date_allowable_qty - $resource->to_date_qty, 2)}}</td>
    <td class="number-cell">{{number_format($resource->to_date_cost, 2)}}</td>
    <td class="number-cell">{{number_format($resource->to_date_allowable, 2)}}</td>
    <td class="number-cell right-border {{$to_date_var < 0? 'text-danger' : 'text-success'}}">{{number_format($to_date_var, 2)}}</td>

    <td class="number-cell">{{number_format($resource->remaining_qty? $resource->remaining_cost / $resource->remaining_qty : 0, 2)}}</td>
    <td class="number-cell">{{number_format($resource->remaining_qty, 2)}}</td>
    <td class="number-cell right-border">{{number_format($resource->remaining_cost, 2)}}</td>

    <td class="number-cell">{{number_format($resource->at_completion_qty? $resource->at_completion_cost / $resource->at_completion_qty : 0, 2)}}</td>
    <td class="number-cell">{{number_format($resource->at_completion_qty, 2)}}</td>
    <td class="number-cell">{{number_format($resource->qty_var, 2)}}</td>
    <td class="number-cell">{{number_format($resource->at_completion_cost, 2)}}</td>
    <td class="number-cell {{$resource->cost_var < 0? 'text-danger' : 'text-success'}}">{{number_format($resource->cost_var, 2)}}</td>
    <td class="number-cell {{$resource->pw_index < 0? 'text-danger' : 'text-success'}}">{{number_format($resource->pw_index * 100, 2)}}%</td>
</tr>
