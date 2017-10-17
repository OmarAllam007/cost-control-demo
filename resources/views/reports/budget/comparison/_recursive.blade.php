<tr class="level-{{ $depth }} child-{{ $wbs_level->parent_id }} {{ $depth ? 'hidden' : '' }}">
    <td class="level-label text-strong" colspan="17" style="width: 100%">
        <a href="#" class="open-level" data-target="child-{{ $wbs_level->id }}" data-open="false">
            <i class="fa fa-plus-square"></i> {{$wbs_level['name']}} &mdash; <small>({{$wbs_level['code']}})</small>
        </a>
    </td>
</tr>

@foreach($wbs_level->subtree as $child)
    @include('reports.budget.comparison._recursive', ['wbs_level' => $child, 'depth' => $depth +1])
@endforeach

@foreach($wbs_level->cost_accounts as $boq)
    <tr class="level-{{$depth + 1}} child-{{$wbs_level->id}} hidden">
        <td style="width: 150px; min-width: 150px;  max-width: 150px;">&nbsp;</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{$boq->cost_account}}</td>
        <td style="width: 200px; min-width: 200px;  max-width: 200px;">{{$boq->description}}</td>
        <td style="width: 75px; min-width: 75px; max-width: 75px;">{{$boq->unit->type ?? ''}}</td>

        {{-- BOQ --}}
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->price_ur, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->quantity, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->boq_cost, 2)}}</td>

        {{-- Dry --}}
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->dry_ur, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->quantity, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->dry_cost, 2)}}</td>

        {{-- Budget --}}
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->budget_qty, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->eng_qty, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->budget_price, 2)}}</td>
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->budget_cost, 2)}}</td>

        {{-- Revised BOQ --}}
        <td style="width: 100px; min-width: 100px;  max-width: 100px;">{{number_format($boq->revised_boq, 2)}}</td>

        {{-- Comparison --}}
        <td style="width: 200px; min-width: 200px;  max-width: 200px;">{{number_format($boq->price_diff, 2)}}</td>
        <td style="width: 200px; min-width: 200px;  max-width: 200px;">{{number_format($boq->qty_diff, 2)}}</td>
    </tr>
@endforeach