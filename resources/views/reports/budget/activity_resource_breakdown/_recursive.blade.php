<tr class="wbs-level level-{{$depth}} child-{{$level->parent_id}} {{$depth? 'hidden' : ''}}">
    <td colspan="6" class="col-sm-10 level-label">
        <a href="#" class="open-level" data-target="child-{{$level->id}}">
            <strong><i class="fa fa-plus-square"></i> {{$level->name}} <small>({{$level->code}})</small></strong>
        </a>
    </td>
    <td class="col-sm-1 text-right"><strong>{{number_format($level->cost, 2)}}</strong></td>
    <td class="col-sm-1"><strong>{{number_format($level->weight, 2)}}%</strong></td>
</tr>

@forelse($level->subtree as $sublevel)
    @include('reports.budget.activity_resource_breakdown._recursive', ['level' => $sublevel, 'depth' => $depth + 1])
@empty
@endforelse

@forelse($level->activities as $activity => $cost_accounts)
    <tr class="level-{{$depth + 1}} activity-level child-{{$level->id}} hidden">
        <td class="col-sm-10 level-label" colspan="6">
            <a href="#" class="open-level" data-target="activity-{{$level->id}}-{{slug($activity)}}">
                <strong><i class="fa fa-plus-square"></i> {{$activity}}</strong>
            </a>
        </td>

        <td class="col-sm-1 text-right"><strong>{{number_format($cost_accounts->flatten()->sum('budget_cost'), 2)}}</strong></td>
        <td class="col-sm-1"><strong>{{number_format($cost_accounts->flatten()->sum('weight'), 2)}}%</strong></td>
    </tr>

    @foreach($cost_accounts as $label => $cost_account)
        <tr class="level-{{$depth + 2}} cost-account-level activity-{{$level->id}}-{{slug($activity)}} hidden">
            <td class="col-sm-10 level-label" colspan="6">
                <a href="#" class="open-level" data-target="resources-{{$level->id}}-{{slug($label)}}">
                    <strong>
                        <i class="fa fa-plus-square"></i>
                        {{$label}} &mdash;
                        @if ($cost_account->get('boq'))
                            ({{$cost_account->get('boq')->description}})
                        @else
                            <span class="text-danger">(BOQ is not found)</span>
                        @endif
                    </strong>
                </a>
            </td>
            <td class="col-sm-1 text-right"><strong>{{number_format($cost_account['cost'], 2)}}</strong></td>
            <td class="col-sm-1"><strong>{{number_format($cost_account['weight'], 2)}}%</strong></td>
        </tr>

        @foreach($cost_account->get('resources') as $resource)
            <tr class="level-{{$depth + 2}} resource-level resources-{{$level->id}}-{{slug($label)}} hidden">
                <td class="col-sm-3">&nbsp;</td>
                <td class="col-sm-2">{{$resource->resource_name}}</td>
                <td class="col-sm-2">{{$resource->resource_type}}</td>
                <td class="col-sm-1">{{number_format($resource->unit_price, 2)}}</td>
                <td class="col-sm-1">{{number_format($resource->budget_unit, 2)}}</td>
                <td class="col-sm-1">{{$resource->measure_unit}}</td>
                <td class="col-sm-1 text-right">{{number_format($resource->budget_cost, 2)}}</td>
                <td class="col-sm-1">{{number_format($resource->weight, 2)}}%</td>
            </tr>
        @endforeach
    @endforeach
@empty
@endforelse