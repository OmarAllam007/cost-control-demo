<tr class="child-{{$type->parent_id}} level-{{$depth}} {{$depth? 'hidden' : ''}}">
    <td colspan="9">
        <a href="#" data-traget="child-{{$type->id}}" class="open-level">{{$type->name}}</a>
    </td>
</tr>

@foreach($type->subtree as $subtype)
    @include('reports.cost-control.waste-index.type', ['type' => $subtype, 'depth' => $depth + 1])
@endforeach

@foreach($type->resources_list as $resource)
    <tr>
        <td class="col-sm-4">{{$resource->resource_name}}</td>
        <td class="col-sm-1">{{number_format($resource->to_date_unit_price, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->to_date_qty, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->allowable_qty, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->qty_var, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->allowable_cost, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->to_date_cost, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->to_date_cost_var, 2)}}</td>
        <td class="col-sm-1">{{number_format($resource->pw_index, 2)}}%</td>
    </tr>
@endforeach