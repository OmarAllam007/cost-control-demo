<tr class="level-{{$depth}} {{$depth? 'hidden' : ''}} chilren-{{$type->parent_id}}">
    <th colspan="4" class="col-sm-10 level-label">
        <a href="#" class="open-level" data-target="chilren-{{$type->id}}">
            <i class="fa fa-plus-square"></i>
            {{$type->name}}
        </a>
    </th>
    <th class="col-sm-2">{{number_format($type->budget_cost, 2)}}</th>
</tr>

@foreach($type->subtypes as $subtype)
    @include('reports.budget.man_power._recursive', ['type' => $subtype, 'depth' => $depth + 1])
@endforeach

@foreach($type->labours as $resource)
    <tr class="level-{{$depth + 1}} hidden chilren-{{$type->id}}">
        <td class="col-sm-2 level-label">{{$resource->resource_code}}</td>
        <td class="col-sm-4">{{$resource->resource_name}}</td>
        <td class="col-sm-2">{{$resource->measure_unit}}</td>
        <td class="col-sm-2">{{number_format($resource->budget_unit, 2)}}</td>
        <td class="col-sm-2">{{number_format($resource->budget_cost, 2)}}</td>
    </tr>
@endforeach