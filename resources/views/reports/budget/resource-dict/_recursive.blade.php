@if ($division->subtree->count() || $division->resources->count())
    <tr class="level-{{$depth}} child-{{$division->parent_id}} {{$depth? 'hidden' : ''}} text-strong">
        <td class="level-label" colspan="6">
            <a href="#" class="open-level" data-target="child-{{$division->id}}">
                <strong><i class="fa fa-plus-square"></i> {{$division->name}}</strong>
            </a>
        </td>
        <td>
            {{number_format($division->budget_cost, 2)}}
        </td>
    </tr>

    @forelse($division->subtree as $subdivision)
        @include('reports.budget.resource-dict._recursive', ['division' => $subdivision, 'depth' => $depth + 1])
    @empty
    @endforelse

    @forelse($division->resources as $resource)
        <tr class="level-{{$depth + 1}} child-{{$division->id}} hidden">
            <td class="level-label">{{$resource->name}}</td>
            <td>{{$resource->resource_code}}</td>
            <td>{{number_format($resource->rate, 2)}}</td>
            <td>{{$resource->units->type ?? ''}}</td>
            <td>{{number_format($resource->waste, 2)}}</td>
            <td>{{number_format($resource->budget_unit, 2)}}</td>
            <td>{{number_format($resource->budget_cost, 2)}}</td>
        </tr>
    @empty
    @endforelse
@endif