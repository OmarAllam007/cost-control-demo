<tr class="info child-{{$type->parent_id}} level-{{$depth}} {{$depth? 'hidden' : ''}}">
    <td colspan="5" class="level-label w-1000">
        <div class="display-flex">
            <a href="#" data-target="child-{{$type->id}}" class="open-level flex">
                <i class="fa fa-plus-square-o"></i>
                {{$type->name}}
            </a>

            <a  href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                data-data="{{ json_encode(['Type' => $type->name, 'Cost Variance - (Waste)' => number_format($type->variance, 2),  'Waste Percentage %' => number_format($type->pw_index, 2) . '%']) }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
    </td>
    <td class="w-150 {{$type->variance < 0? 'text-danger' : ''}}">{{number_format($type->variance, 2)}}</td>
    <td class="w-150 {{$type->pw_index < 0? 'text-danger' : ''}}">{{number_format($type->pw_index, 2)}}%</td>
</tr>

@foreach($type->subtree as $subtype)
    @include('reports.cost-control.waste-index.type', ['type' => $subtype, 'depth' => $depth + 1])
@endforeach

@foreach($type->resources_list as $resource)
    <tr class="level-{{$depth+1}} child-{{$type->id}} hidden">
        <td class="w-400 level-label">
            <div class="display-flex">
                <span class="flex">{{$resource->resource_name}}</span>

                <a  href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                    data-data="{{ json_encode(['Resource' => $resource->resource_name, 'To Date Price/ Unit' => number_format($resource->to_date_unit_price, 2),  'To date Quantity' => number_format($resource->to_date_qty, 2),  'Allowable QTY' => number_format($resource->allowable_qty, 2),  'Quantity +/-' => number_format($resource->qty_var, 2),  'Cost Variance - (Waste)' => number_format($resource->variance, 2),  'Waste Percentage %' => number_format($resource->pw_index, 2) . '%']) }}">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                </a>
            </div>
        </td>
        <td class="w-150">{{number_format($resource->to_date_unit_price, 2)}}</td>
        <td class="w-150">{{number_format($resource->to_date_qty, 2)}}</td>
        <td class="w-150">{{number_format($resource->allowable_qty, 2)}}</td>
        <td class="w-150 {{$resource->qty_var < 0? 'text-danger' : ''}}">{{number_format($resource->qty_var, 2)}}</td>
        <td class="w-150 {{$resource->variance < 0? 'text-danger' : ''}}">{{number_format($resource->variance, 2)}}</td>
        <td class="w-150 {{$resource->pw_index < 0? 'text-danger' : ''}}">{{number_format($resource->pw_index, 2)}}%</td>
    </tr>
@endforeach