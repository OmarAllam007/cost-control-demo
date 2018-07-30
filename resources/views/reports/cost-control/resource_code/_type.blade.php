<tr class="resource-type type-{{$type->parent_id}} level-{{$depth}} {{$depth? 'hidden' : ''}}">
    <td class="resource-cell right-border level-label">
        <div class="display-flex">
            <strong class="flex"><a href="#" data-target=".type-{{$type->id}}"><i class="fa fa-plus-square-o"></i> {{$type->name}}</a></strong>

            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{collect([
                            'Division' => $type->path, 'Budget Cost' => number_format($type->budget_cost,2), 'Previous Cost' => number_format($type->prev_cost,2),
                            'Current Cost' => number_format($type->curr_cost,2),
                            'To Date Cost' => number_format($type->to_date_cost, 2),
                            'Allowable Cost' => number_format($type->to_date_allowable, 2),
                            'To Date Cost Var' => number_format($type->to_date_cost_var, 2),
                            'Remaining Cost' => number_format($type->remaining_cost, 2),
                            'At Completion Cost' => number_format($type->at_completion_cost, 2),
                            'At Completion Cost Var' => number_format($type->cost_var, 2),
                           ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
    </td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($type->budget_cost, 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($type->prev_cost, 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($type->curr_cost, 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">{{number_format($type->to_date_cost, 2)}}</td>
    <td class="number-cell">{{number_format($type->to_date_allowable, 2)}}</td>
    <td class="number-cell right-border {{$type->to_date_cost_var < 0? 'text-danger' : 'text-success'}}">{{number_format($type->to_date_cost_var, 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell right-border">{{number_format($type->remaining_cost, 2)}}</td>

    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">&nbsp;</td>
    <td class="number-cell">{{number_format($type->at_completion_cost, 2)}}</td>
    <td class="number-cell {{$type->cost_var < 0? 'text-danger' : 'text-success'}}">{{number_format($type->cost_var, 2)}}</td>
    <td class="number-cell"></td>
</tr>


@foreach ($type->subtree as $sub_type_id => $subtype)
    @include('reports.cost-control.resource_code._type', ['type_id' => $sub_type_id, 'type' => $subtype, 'depth' => $depth + 1])
@endforeach

@foreach($type->db_resources as $resource)
    @include('reports.cost-control.resource_code._resource', ['depth' => $depth + 1])
@endforeach