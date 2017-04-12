<tr class="success">
    <td class="resource-cell right-border">
        <a href="#" data-target="{{slug($type)}}"><i class="fa fa-plus-circle"></i> {{$type}}</a>
    </td>

    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell right-border"></td>

    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell right-border"></td>

    <td class="number-cell"></td>
    <td class="number-cell right-border"></td>
</tr>

@foreach ($typeData as $discipline => $disciplineData)
    <tr class="discipline {{slug($type)}} hidden">
        <td class="resource-cell right-border">
            <a href="#" data-target="{{slug($type)}}-{{slug($discipline ?: 'General')}}"><i class="fa fa-plus-circle"></i> {{$discipline ?: 'General'}}</a>
        </td>

        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell right-border"></td>

        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell right-border"></td>

        <td class="number-cell"></td>
        <td class="number-cell right-border"></td>
    </tr>

    @foreach($disciplineData as $resource)
        <tr class="{{slug($type)}}-{{slug($discipline ?: 'General')}} resource hidden">
            <td class="resource-cell right-border">
                <i class="fa fa-caret-right"></i> {{$resource->resource_name}}
            </td>

            <td class="number-cell">{{number_format($resource->budget_unit_price, 2)}}</td>
            <td class="number-cell">{{number_format($resource->prev_unit_price, 2)}}</td>
            <td class="number-cell">{{number_format($resource->curr_unit_price, 2)}}</td>
            <td class="number-cell">{{number_format($resource->to_date_unit_price, 2)}}</td>
            @php $price_var = $resource->budget_unit_price - $resource->to_date_unit_price @endphp
            <td class="number-cell {{$price_var < 0? 'text-danger' : 'text-success'}}">{{number_format($price_var, 2)}}</td>
            <td class="number-cell {{$price_var < 0? 'text-danger' : 'text-success'}} right-border">{{number_format($price_var * $resource->to_date_qty, 2)}}</td>

            <td class="number-cell">{{number_format($resource->to_date_qty, 2)}}</td>
            <td class="number-cell">{{number_format($resource->to_date_allowable_qty, 2)}}</td>
            @php $qty_var = $resource->to_date_allowable_qty - $resource->to_date_qty @endphp
            <td class="number-cell {{$qty_var < 0? 'text-danger' : 'text-success'}}">{{number_format($qty_var, 2)}}</td>
            <td class="number-cell {{$qty_var < 0? 'text-danger' : 'text-success'}} right-border">{{number_format($qty_var * $resource->budget_unit_price, 2)}}</td>

            <td class="number-cell {{$resource->cost_unit_price_var <0? 'text-danger' : 'text-success'}}">{{number_format($resource->cost_unit_price_var, 2)}}</td>
            <td class="number-cell {{$resource->cost_qty_var <0? 'text-danger' : 'text-success'}} right-border">{{number_format($resource->cost_qty_var, 2)}}</td>
        </tr>
    @endforeach
@endforeach