@if ($division->subtree->count() || $division->productivities->count())
    <tr class="level-{{$depth}} child-{{$division->parent_id}} {{$depth? 'hidden' : ''}} text-strong">
        <td class="level-label" colspan="4">
            <a href="#" class="open-level" data-target="child-{{$division->id}}">
                <strong><i class="fa fa-plus-square"></i> {{$division->name}}</strong>
            </a>
        </td>
    </tr>

    @forelse($division->subtree as $subdivision)
        @include('reports.budget.productivity._recursive', ['division' => $subdivision, 'depth' => $depth + 1])
    @empty
    @endforelse

    @forelse($division->productivities as $productivity)
        <tr class="level-{{$depth + 1}} child-{{$division->id}} hidden">
            <td class="level-label col-sm-5">{{$productivity->description}}</td>
            <td class="cols-m-3">{!! nl2br(e($productivity->crew_structure)) !!}</td>
            <td class="cols-m-2">{{$productivity->after_reduction}}</td>
            <td class="cols-m-2">{{$productivity->units->type}}</td>
        </tr>
    @empty
    @endforelse
@endif