@extends('layouts.app')

@section('header')
    <h4>{{$project->name}} &mdash; Material &mdash; Physical Quantity</h4>
    <h4 class="pull-right text-muted">#E08</h4>
@endsection

@section('body')
    {{Form::open()}}

    @foreach($resources as $activity => $sub_resources)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activity}}</h4>
            </div>

            <table class="table table-condensed table-bordered table-striped">
                <thead>
                <tr>
                    <th>Cost Account</th>
                    <th>Budget Resource Code</th>
                    <th>Budget Resource Name</th>
                    <th>Budget Unit</th>
                    <th>Previous Qty</th>
                    <th>Budget U.O.M</th>
                    <th>Store Resource Code</th>
                    <th>Store Resource Name</th>
                    <th>Store Qty</th>
                    <th>Store U.O.M</th>
                    <th>Physical Qty</th>
                    <th>Physical Unit Price</th>
                    <th>Total Amount</th>
                </tr>

                </thead>
                <tbody>
                @foreach($sub_resources as $resource)
                    @foreach($resource['resources'] as $i => $store_resource)
                        <tr>
                            @if ($i == 0)
                                <td rowspan="{{$resource['resources']->count()}}">{{$resource['target']->cost_account}}</td>
                                <td rowspan="{{$resource['resources']->count()}}">{{$resource['target']->resource_code}}</td>
                                <td rowspan="{{$resource['resources']->count()}}">{{$resource['target']->resource_name}}</td>
                                <td rowspan="{{$resource['resources']->count()}}">{{$resource['target']->budget_unit}}</td>
                                <td rowspan="{{$resource['resources']->count()}}">{{number_format($resource['target']->cost->previous_qty ?? 0, 2)}}</td>
                                <td rowspan="{{$resource['resources']->count()}}">{{$resource['target']->measure_unit}}</td>
                            @endif
                            <td>{{$store_resource['original_data'][7]}}</td>
                            <td>{{$store_resource['original_data'][2]}}</td>
                            <td>{{number_format($store_resource['original_data'][4], 2)}}</td>
                            <td>{{$store_resource['original_data'][3]}}</td>
                            @if ($i == 0)
                                <td rowspan="{{$resource['resources']->count()}}">
                                    {{Form::text("quantities[{$resource['target']->breakdown_resource_id}]", '0.00', ['class' => 'form-control input-sm physical-qty'])}}
                                </td>
                                <td rowspan="{{$resource['resources']->count()}}" class="unit-price-cell">0.00</td>
                                <td rowspan="{{$resource['resources']->count()}}" class="total-cell"
                                    data-value="{{$resource['resources']->sum('cost')}}">
                                    {{number_format($resource['resources']->sum('cost'), 2)}}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="form-group">
        <button class="btn btn-primary">Next <i class="fa fa-chevron-circle-right"></i></button>
    </div>
    {{Form::close()}}
@endsection

@section('javascript')
    <script>
        $(function () {
            $('.physical-qty').on('change', function () {
                var parent = $(this).closest('tr');
                var total = parseFloat(parent.find('.total-cell').data('value'));
                var qty = parseFloat($(this).val());
                var unit_price = 0;

                if (qty) {
                    unit_price = total / qty;
                }

                var unit_price_formatted = parseFloat(unit_price.toFixed(2)).toLocaleString({style: 'currency'});
                parent.find('.unit-price-cell').text(unit_price_formatted);
            });
        });
    </script>
@endsection