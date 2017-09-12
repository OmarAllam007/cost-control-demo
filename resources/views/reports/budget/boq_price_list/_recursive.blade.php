<tr class="level-{{$depth}} child-{{$wbs_level->parent_id}} {{$depth? 'hidden' : ''}}">
    <td class="level-label" colspan="12">
        <a href="#" class="open-level" data-target="child-{{$wbs_level->id}}">
            <strong><i class="fa fa-plus-square"></i> {{$wbs_level->name}}</strong>
        </a>
    </td>
    {{--<td><strong>{{number_format($wbs_level->cost, 2)}}</strong></td>--}}
</tr>

@forelse($wbs_level->subtree as $sublevel)
    @include('reports.budget.boq_price_list._recursive', ['wbs_level' => $sublevel, 'depth' => $depth + 1])
@empty
@endforelse

@forelse($wbs_level->cost_accounts->sortBy('description') as $cost_account)
    <tr class="level-{{$depth + 1}} child-{{$wbs_level->id}} hidden">
        <td style="min-width: 400px;max-width: 400px;" class=" level-label">{{$cost_account['description']}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{$cost_account['cost_account']}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['budget_qty'], 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{$cost_account['unit_of_measure']}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['01.general requirment'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['02.labors'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['03.material'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['04.subcontractors'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['05.equipment'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['06.scaffolding'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['types']['07.others'] ?? 0, 2)}}</td>
        <td style="min-width: 150px;max-width: 150px;" class="">{{number_format($cost_account['grand_total'], 2)}}</td>
    </tr>
@empty
@endforelse