<tr class="success">
    <td class="resource-cell right-border">
        <a href="#" data-target="{{slug($type)}}"><i class="fa fa-plus-circle"></i> <strong>{{$type}}</strong></a>
    </td>

    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell right-border"><strong>{{number_format($typeData['price_cost_var'], 2)}}</strong></td>

    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell"></td>
    <td class="number-cell right-border"><strong>{{number_format($typeData['qty_cost_var'], 2)}}</strong></td>

    <td class="number-cell"><strong>{{number_format($typeData['cost_qty_var'], 2)}}</strong></td>
    <td class="number-cell right-border"><strong>{{number_format($typeData['cost_unit_price_var'], 2)}}</strong></td>
</tr>

@foreach ($typeData['disciplines'] as $discipline => $disciplineData)
    <tr class="discipline {{slug($type)}} hidden">
        <td class="resource-cell right-border">
            <a href="#" data-target="{{slug($type)}}-{{slug($discipline ?: 'General')}}"><i class="fa fa-plus-circle"></i> {{$discipline ?: 'General'}}</a>
        </td>

        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell right-border">{{number_format($disciplineData['price_cost_var'], 2)}}</td>

        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell"></td>
        <td class="number-cell right-border">{{number_format($disciplineData['qty_cost_var'], 2)}}</td>

        <td class="number-cell">{{number_format($disciplineData['cost_qty_var'], 2)}}</td>
        <td class="number-cell right-border">{{number_format($disciplineData['cost_unit_price_var'], 2)}}</td>
    </tr>

    @foreach($disciplineData['resources'] as $resource)
        <tr class="{{slug($type)}}-{{slug($discipline ?: 'General')}} resource hidden">
            <td class="resource-cell right-border">
                <i class="fa fa-caret-right"></i> {{$resource->resource_name}}
            </td>

            <td class="number-cell">{{number_format($resource->budget_unit_price, 2)}}</td>
            <td class="number-cell">{{number_format($resource->prev_unit_price, 2)}}</td>
            <td class="number-cell">{{number_format($resource->curr_unit_price, 2)}}</td>
            <td class="number-cell">{{number_format($resource->to_date_unit_price, 2)}}</td>

            <td class="number-cell {{$resource->price_var < 0? 'text-danger' : 'text-success'}}">{{number_format($resource->price_var, 2)}}</td>
            <td class="number-cell {{$resource->price_var < 0? 'text-danger' : 'text-success'}} right-border">{{number_format($resource->price_cost_var, 2)}}</td>

            <td class="number-cell">{{number_format($resource->to_date_qty, 2)}}</td>
            <td class="number-cell">{{number_format($resource->to_date_allowable_qty, 2)}}</td>

            <td class="number-cell {{$resource->qty_var < 0? 'text-danger' : 'text-success'}}">{{number_format($resource->qty_var, 2)}}</td>
            <td class="number-cell {{$resource->qty_var < 0? 'text-danger' : 'text-success'}} right-border">{{number_format($resource->qty_cost_var, 2)}}</td>

            <td class="number-cell {{$resource->cost_unit_price_var <0? 'text-danger' : 'text-success'}}">{{number_format($resource->cost_unit_price_var, 2)}}</td>
            <td class="number-cell {{$resource->cost_qty_var <0? 'text-danger' : 'text-success'}} right-border">{{number_format($resource->cost_qty_var, 2)}}</td>
        </tr>
    @endforeach
@endforeach