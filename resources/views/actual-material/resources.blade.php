@extends('layouts.app')

@section('header')
    <h4>{{$project->name}} &mdash; Material &mdash; Physical Quantity</h4>
    <h4 class="pull-right text-muted">#E02</h4>
@endsection

@section('body')
    {{Form::open()}}
    @foreach($shadows as $code => $activityData)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activityData['name']}}</h4>
            </div>

            <table class="table table-condensed table-bordered table-striped">
                <thead>
                <tr>
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
                @foreach($activityData['resources'] as $resource_id => $shadow)
                    @php
                    $code = mb_strtolower($code);
                    @endphp

                    @if (!isset($resources[$code][$resource_id]))
                        @continue;
                    @endif

                    @php
                    $sub_resources = collect($resources[$code][$resource_id]);
                    $row_span = count($sub_resources);
                    $counter= 0;
                    @endphp
                    @foreach($sub_resources as $store_resource)
                        <tr>
                            @if ($counter == 0)
                                <td rowspan="{{$row_span}}">{{$shadow->resource_code}}</td>
                                <td rowspan="{{$row_span}}">{{$shadow->resource_name}}</td>
                                <td rowspan="{{$row_span}}">{{$shadow->budget_unit}}</td>
                                <td rowspan="{{$row_span}}">{{number_format($shadow->cost->previous_qty ?? 0, 2)}}</td>
                                <td rowspan="{{$row_span}}">{{$shadow->measure_unit}}</td>
                            @endif
                            <td>{{$store_resource[7]}}</td>
                            <td>{{$store_resource[2]}}</td>
                            <td>{{number_format($store_resource[4], 2)}}</td>
                            <td>{{$store_resource[3]}}</td>
                            @if ($counter == 0)
                                <td rowspan="{{$row_span}}">
                                    {{Form::text("quantities[{$code}][{$resource_id}]", '0.00', ['class' => 'form-control input-sm physical-qty'])}}
                                </td>
                                <td rowspan="{{$row_span}}" class="unit-price-cell">0.00</td>
                                <td rowspan="{{$row_span}}" class="total-cell"
                                    data-value="{{$sub_resources->sum(6)}}">
                                    {{number_format($sub_resources->sum(6), 2)}}
                                </td>
                            @endif
                        </tr>

                        @php $counter++; @endphp
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
            }).keydown(function (e) {
                if (e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 9) {
                    e.preventDefault();
                    var nextRow = $(this).closest('tr').next();
                    while (nextRow.length && nextRow.find('.physical-qty').length == 0) {
                        nextRow = nextRow.next();
                    }
                    nextRow.find('.physical-qty').focus();
                } else if (e.keyCode == 38) {
                    var prevRow = $(this).closest('tr').prev();
                    while (prevRow.length && prevRow.find('.physical-qty').length == 0) {
                        prevRow = prevRow.prev();
                    }
                    prevRow.find('.physical-qty').focus();
                }
            }).first().focus();;
        });
    </script>
@endsection