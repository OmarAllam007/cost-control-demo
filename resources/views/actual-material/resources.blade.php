@extends('layouts.app')

@section('header')
    <h4>{{$project->name}} &mdash; Material &mdash; Physical Quantity</h4>
    <h4 class="pull-right text-muted">#E02</h4>
@endsection

@section('body')
    {{Form::open()}}
    @foreach($activities as $activity => $activityData)
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activity}}</h4>
            </div>

            <table class="table table-condensed table-bordered">
                <thead>
                <tr>
                    <th>Budget Resource Code</th>
                    <th>Budget Resource Name</th>
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
                @foreach($activityData as $activityResourceCounter => $resource)
                    @php
                    $row_span = count($resource['rows']);
                    @endphp

                    @foreach($resource['rows'] as $counter => $store_resource)
                        <tr class="resource-{{$activityResourceCounter}}">
                            @if ($counter == 0)
                                <td rowspan="{{$row_span}}">{{$resource['resource']->resource_code}}</td>
                                <td rowspan="{{$row_span}}">{{$resource['resource']->resource_name}}</td>
                                <td rowspan="{{$row_span}}">{{$resource['resource']->measure_unit}}</td>
                            @endif
                            <td>{{$store_resource[7]}}</td>
                            <td>{{$store_resource[2]}}</td>
                            <td class="store-qty" data-qty="{{$store_resource[4]}}">{{number_format($store_resource[4], 2)}}</td>
                            <td>{{$store_resource[3]}}</td>
                            @if ($counter == 0)
                                <td rowspan="{{$row_span}}">
                                    <div class="input-group">
                                        {{Form::text("quantities[{$resource['resource']->id}]", '0.00', ['class' => 'form-control input-sm physical-qty'])}}
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary btn-sm sum-qty" data-counter="{{$activityResourceCounter}}" title="SUM">&sum;</button>
                                        </span>
                                    </div>
                                    {!! $errors->first("quantities.{$resource['resource']->id}", '<div class="text-danger">:message</div>') !!}
                                </td>
                                <td rowspan="{{$row_span}}" class="unit-price-cell">0.00</td>
                                <td rowspan="{{$row_span}}" class="total-cell"
                                    data-value="{{$total = $resource['rows']->sum(6)}}">
                                    {{number_format($total, 2)}}
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
            }).first().focus();

            $('.sum-qty').on('click', function(e) {
                e.preventDefault();

                var _class = '.resource-' + $(this).data('counter');
                var total = 0;
                $(this).closest('table').find(_class).each(function (idx, elem) {
                    total += parseFloat($(elem).find('.store-qty').data('qty'));
                });

                $(this).closest('.input-group').find('.physical-qty').val(total).change();
            });
        });
    </script>
@endsection