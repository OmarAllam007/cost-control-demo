<tr class="success">
    <td class="resource-cell right-border">
        <div class="display-flex">
            <a href="#" data-target="{{slug($type)}}" class="flex"><i class="fa fa-plus-circle"></i>
                <strong>{{$type}}</strong></a>

            <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
               data-data="{{
                json_encode([
                    'Resource Type' => $type, 'Unit Price Analysis / To Date Cost Var' => number_format($typeData['price_cost_var'], 2),
                    'Quantity Analysis / To Date Cost Var' => number_format($typeData['qty_cost_var'], 2),
                    'At Completion Var due to Unit Price Var' => number_format($typeData['cost_unit_price_var'], 2),
                    'At Completion Var due to Qty Var' => number_format($typeData['cost_qty_var'], 2)
                    ])
                    }}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </a>
        </div>
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

    <td class="number-cell right-border"><strong>{{number_format($typeData['cost_unit_price_var'], 2)}}</strong></td>
    <td class="number-cell"><strong>{{number_format($typeData['cost_qty_var'], 2)}}</strong></td>
</tr>

@foreach ($typeData['disciplines'] as $discipline => $disciplineData)
    <tr class="discipline {{slug($type)}} hidden">
        <td class="resource-cell right-border">
            <div class="display-flex">
                <a href="#" data-target="{{slug($type)}}-{{slug($discipline ?: 'General')}}" class="flex">
                    <i class="fa fa-plus-circle"></i> {{$discipline ?: 'General'}}</a>

                <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                   data-data="{{
                    json_encode([
                        'Resource Type' => "$type / $discipline", 'Unit Price Analysis / To Date Cost Var' => number_format($disciplineData['price_cost_var'], 2),
                        'Quantity Analysis / To Date Cost Var' => number_format($disciplineData['qty_cost_var'], 2),
                        'At Completion Var due to Unit Price Var' => number_format($disciplineData['cost_unit_price_var'], 2),
                        'At Completion Var due to Qty Var' => number_format($disciplineData['cost_qty_var'], 2)
                    ])
                    }}">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                </a>
            </div>
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

        <td class="number-cell right-border">{{number_format($disciplineData['cost_unit_price_var'], 2)}}</td>
        <td class="number-cell">{{number_format($disciplineData['cost_qty_var'], 2)}}</td>
    </tr>

    @foreach($disciplineData['resources'] as $resource)
        <tr class="{{slug($type)}}-{{slug($discipline ?: 'General')}} resource hidden">
            <td class="resource-cell right-border">
                <div class="display-flex">
                    <span class="flex"><i class="fa fa-caret-right"></i> {{$resource->resource_name}}</span>

                    <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                       data-data="{{
                        json_encode([
                            'Resource' => "$type / $discipline / $resource->resource_name",
                            'Unit Price Analysis / Budget Price/Unit' => number_format($resource->budget_unit_price, 2),
                            'Unit Price Analysis / Previous Price/Unit' => number_format($resource->prev_unit_price, 2),
                            'Unit Price Analysis / Current Price/Unit' => number_format($resource->curr_unit_price, 2),
                            'Unit Price Analysis / To Date Price/Unit' => number_format($resource->to_date_unit_price, 2),
                            'Unit Price Analysis / Price/Unit Var' => number_format($resource->price_var, 2),
                            'Unit Price Analysis / To Date Cost Var' => number_format($resource->price_cost_var, 2),

                            'Quantity Analysis / To Date Qty' => number_format($resource->to_date_qty, 2),
                            'Quantity Analysis / Allowable Qty' => number_format($resource->to_date_allowable_qty, 2),
                            'Quantity Analysis / Quantity Var' => number_format($resource->qty_var, 2),
                            'Quantity Analysis / To Date Cost Var' => number_format($resource->qty_cost_var, 2),

                            'At Completion Var due to Unit Price Var' => number_format($resource->cost_unit_price_var, 2),
                            'At Completion Var due to Qty Var' => number_format($resource->cost_qty_var, 2)
                        ])
                        }}">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </a>
                </div>

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
            <td class="number-cell {{$resource->qty_cost_var < 0? 'text-danger' : 'text-success'}} right-border">{{number_format($resource->qty_cost_var, 2)}}</td>

            <td class="number-cell {{$resource->cost_unit_price_var <0? 'text-danger' : 'text-success'}}">{{number_format($resource->cost_unit_price_var, 2)}}</td>
            <td class="number-cell {{$resource->cost_qty_var <0? 'text-danger' : 'text-success'}} right-border">{{number_format($resource->cost_qty_var, 2)}}</td>
        </tr>
    @endforeach
@endforeach