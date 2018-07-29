<tr class="activity level-{{$depth}} {{slug($key)}} {{$depth > 1? 'hidden' : ''}}">
    <td>
        <div class="display-flex">
            <a href="#" class="open-level flex" data-target="{{slug($key .'-'.$name)}}"><i class="fa fa-plus-square-o"></i> {{$activity['name']}}</a>
            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{json_encode([
                            'Activity' => $level['parent'] . $level['name'] . ' / ' . $name,
                            'Base Line' => number_format($activity['budget_cost'], 2),
                            'Previous Cost' => number_format($activity['prev_cost'], 2),
                            'Previous Allowable' => number_format($activity['prev_allowable'], 2),
                            'Previous Var' => number_format($activity['prev_cost_var'], 2),
                            'To Date Cost' => number_format($activity['to_date_cost'], 2),
                            'Allowable (EV) Cost' => number_format($activity['to_date_allowable'], 2),
                            'To Date Cost Var' => number_format($activity['to_date_var'], 2),
                            'Remaining Cost' => number_format($activity['remaining_cost'], 2),
                            'At Completion Cost' => number_format($activity['completion_cost'], 2),
                            'Cost Variance' => number_format($activity['completion_var'], 2),
                           ]) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
    </td>
    <td>{{number_format($activity['budget_cost'], 2)}}</td>

    <td>{{number_format($activity['prev_cost'], 2)}}</td>
    <td>{{number_format($activity['prev_allowable'], 2)}}</td>
    <td class="{{$activity['prev_cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($activity['prev_cost_var'], 2)}}</td>

    <td>{{number_format($activity['to_date_cost'], 2)}}</td>
    <td>{{number_format($activity['to_date_allowable'], 2)}}</td>
    <td class="{{$activity['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($activity['to_date_var'], 2)}}</td>

    <td>{{number_format($activity['remaining_cost'], 2)}}</td>

    <td>{{number_format($activity['completion_cost'], 2)}}</td>
    <td class="{{$activity['completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($activity['completion_var'], 2)}}</td>
</tr>


@foreach($activity['resources'] as $resource)
    <tr class="resource level-{{$depth + 1}} {{slug($key .'-'.$name)}} hidden">
        <td>
            <div class="display-flex">
                <span class="flex"><i class="fa fa-angle-right"></i> {{$resource->resource_name}}</span>
                <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                   data-data="{{json_encode([
                            'Resource' => $level['parent'] . $level['name'] . ' / ' . $name . ' / ' . $resource->resource_name,
                            'Base Line' => number_format($resource['budget_cost'], 2),
                            'Previous Cost' => number_format($resource['prev_cost'], 2),
                            'Previous Allowable' => number_format($resource['prev_allowable'], 2),
                            'Previous Var' => number_format($resource['prev_cost_var'], 2),
                            'To Date Cost' => number_format($resource['to_date_cost'], 2),
                            'Allowable (EV) Cost' => number_format($resource['to_date_allowable'], 2),
                            'To Date Cost Var' => number_format($resource['to_date_var'], 2),
                            'Remaining Cost' => number_format($resource['remaining_cost'], 2),
                            'At Completion Cost' => number_format($resource['completion_cost'], 2),
                            'Cost Variance' => number_format($resource['completion_var'], 2),
                           ]) }}">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                </a>
            </div>
        </td>
        <td>{{number_format($resource['budget_cost'], 2)}}</td>

        <td>{{number_format($resource['prev_cost'], 2)}}</td>
        <td>{{number_format($resource['prev_allowable'], 2)}}</td>
        <td class="{{$resource['prev_cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($resource['prev_cost_var'], 2)}}</td>

        <td>{{number_format($resource['to_date_cost'], 2)}}</td>
        <td>{{number_format($resource['to_date_allowable'], 2)}}</td>
        <td class="{{$resource['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($resource['to_date_var'], 2)}}</td>

        <td>{{number_format($resource['remaining_cost'], 2)}}</td>

        <td>{{number_format($resource['completion_cost'], 2)}}</td>
        <td class="{{$resource['completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($resource['completion_var'], 2)}}</td>
    </tr>
@endforeach