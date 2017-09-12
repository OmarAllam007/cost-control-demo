@php $hasChildren = $division->subtree->count() || $division->std_activities->count() @endphp

@if ($hasChildren)
    <tr class="level-{{$depth}} text-strong {{$depth? "hidden child-{$division->parent_id}" : ''}}">
        <td class="level-label col-sm-8">
            <a href="#" data-target="child-{{$division->id}}" class="open-level">
                <i class="fa fa-plus-square"></i> {{$division->code}} {{$division->name}}
            </a>
        </td>
        @if ($includeCost)
            <td class="col-sm-4">{{number_format($division->cost, 2)}}</td>
        @endif
    </tr>

    @if ($division->subtree->count())
        @foreach($division->subtree as $subdivision)
            @include('reports.budget.std-activity._recursive', ['division' => $subdivision, 'depth' => $depth + 1])
        @endforeach
    @endif

    @if ($division->std_activities->count())
        @foreach($division->std_activities as $activity)
            <tr class="level-{{$depth + 1}} hidden child-{{$activity->division_id}}">
                <td class="level-label">{{$activity->name}}</td>
                @if ($includeCost)
                    <td>{{number_format($activity->cost, 2)}}</td>
                @endif
            </tr>
        @endforeach
    @endif
@endif
