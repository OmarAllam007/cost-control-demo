<tr class="info child-{{$type->parent_id}} level-{{$depth}} {{$depth? 'hidden' : ''}}">
    <td colspan="5" class="level-label w-1000">
        <a href="#" data-target="child-{{$type->id}}" class="open-level">
            <i class="fa fa-plus-square-o"></i>
            {{$type->name}}
        </a>
    </td>
    <td class="w-150 {{$type->variance < 0? 'text-danger' : ''}}">{{number_format($type->variance, 2)}}</td>
    <td class="w-150 {{$type->pw_index < 0? 'text-danger' : ''}}">{{number_format($type->pw_index, 2)}}%</td>
</tr>

@foreach($type->subtree as $subtype)
    @include('reports.cost-control.waste-index.type', ['type' => $subtype, 'depth' => $depth + 1])
@endforeach

@foreach($type->resources_list as $resource)
    <tr class="level-{{$depth+1}} child-{{$type->id}} hidden">
        <td class="w-400 level-label">{{$resource->resource_name}}</td>
        <td class="w-150">{{number_format($resource->to_date_unit_price, 2)}}</td>
        <td class="w-150">{{number_format($resource->to_date_qty, 2)}}</td>
        <td class="w-150">{{number_format($resource->allowable_qty, 2)}}</td>
        <td class="w-150 {{$resource->qty_var < 0? 'text-danger' : ''}}">{{number_format($resource->qty_var, 2)}}</td>
        <td class="w-150 {{$resource->variance < 0? 'text-danger' : ''}}">{{number_format($resource->variance, 2)}}</td>
        <td class="w-150 {{$resource->pw_index < 0? 'text-danger' : ''}}">{{number_format($resource->pw_index, 2)}}%</td>
    </tr>
@endforeach