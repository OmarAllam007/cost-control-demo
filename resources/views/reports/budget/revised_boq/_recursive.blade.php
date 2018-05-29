<tr class="level-{{$depth}} child-{{$wbs_level->parent_id}} {{$depth? 'hidden' : ''}}">
    <th class="level-label col-sm-3">
        <a href="#" class="open-level" data-target="child-{{$wbs_level->id}}">
            <i class="fa fa-plus-square"></i> {{$wbs_level->name}} <small>({{$wbs_level->code}})</small>
        </a>
    </th>
    <th class="col-sm-2"></th>
    <th class="col-sm-1"></th>
    <th class="col-sm-1"></th>
    <th class="col-sm-1"></th>
    <th class="col-sm-1">{{number_format($wbs_level->original_boq, 2)}}</th>
    <th class="col-sm-1"></th>
    <th class="col-sm-2">{{number_format($wbs_level->revised_boq, 2)}}</th>
</tr>

@foreach($wbs_level->subtree as $sub_level)
    @include('reports.budget.revised_boq._recursive', ['wbs_level' => $sub_level, 'depth' => $depth + 1])
@endforeach

@foreach($wbs_level->cost_accounts as $cost_account)
    <tr class="level-{{$depth + 2}} child-{{$wbs_level->id}} hidden">
        <td class="col-sm-3 level-label">{{$cost_account->description}}</td>
        <td>{{$cost_account->cost_account}}</td>
        <td>{{number_format($cost_account->price_ur, 2)}}</td>
        <td>{{number_format($cost_account->estimated_qty, 2)}}</td>
        <td>{{$cost_account->unit->type ?? ''}}</td>
        <td>{{number_format($cost_account->original_boq, 2)}}</td>
        <td>{{number_format($cost_account->budget_qty, 2)}}</td>
        <td>{{number_format($cost_account->revised_boq, 2)}}</td>
    </tr>
@endforeach