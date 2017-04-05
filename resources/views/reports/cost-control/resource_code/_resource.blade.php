<tr class="{{$class}} {{$slug}} hidden">
    <td class="resource-cell right-border"><i class="fa fa-caret-right"></i> {{$resource->resource_name}}</td>

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
    @php $to_date_var = $resource->to_date_allowable - $resource->to_date_cost; @endphp
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
