@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash; Unit Mismatch</h4>
        <h4 class="pull-right text-muted">E#02</h4>
    </div>
@endsection

@section('body')
    {{Form::open(['method' => 'post'])}}
    @foreach($units as $activity => $resources)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activity}}</h4>
            </div>
            <table class="table table-bordered table-condensed table-hover table-striped" data-total="{{$resources->first()[6]}}">
                <thead>
                <tr>
                    <th>Activity</th>
                    <th>Resource Code</th>
                    <th>Store Resource Name</th>
                    <th>Budget Resource Code</th>
                    <th>Budget Resource Name</th>
                    <th>Cost Account</th>
                    <th>Budget Qty</th>
                    <th>Budget Cost</th>
                    <th>Budget Unit</th>
                    <th>Store U.O.M</th>
                    <th>Budget U.O.M</th>
                    <th>Store Qty</th>
                    <th>Store Unit Price</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $idx => $line)
                    <tr>
                        <td>
                            {{$line['resource']->breakdown_resource->code}}
                        </td>
                        <td>
                            {{$line[7]}}
                        </td>
                        <td>
                            {{$line[2]}}
                        </td>
                        <td>
                            {{$line['resource']->resource_code}}
                        </td>
                        <td>
                            {{$line['resource']->resource_name}}
                        </td>

                        <td>
                            {{$line['resource']->cost_account}}
                        </td>
                        <td>
                            {{number_format($line['resource']->budget_qty, 2)}}
                        </td>
                        <td>
                            {{number_format($line['resource']->budget_cost, 2)}}
                        </td>
                        <td>
                            {{number_format($line['resource']->budget_unit, 2)}}
                        </td>
                        <td>
                            {{ $line[3] }}
                        </td>
                        <td>
                            {{ $line['resource']->measure_unit }}
                        </td>
                        <td>
                            {{ number_format($line[4], 2) }}
                        </td>

                        <td>
                            {{number_format($line[5], 2)}}
                        </td>

                        <td>
                            {{Form::text("units[{$line['resource']->breakdown_resource_id}][qty]", 0, ['class' => 'form-control input-sm qty', 'tabindex' => $idx])}}
                        </td>
                        <td>
                            {{Form::text("units[{$line['resource']->breakdown_resource_id}][unit_price]", 0, ['class' => 'form-control input-sm unit-price', 'readonly', 'tabindex' => -1])}}
                        </td>
                        <td class="total-price">
                            {{ number_format(0, 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="form-group">
        <button class="btn btn-success">Next <i class="fa fa-chevron-circle-right"></i></button>
    </div>

    {{Form::close()}}

@endsection

@section('javascript')
    <script>
        $(function () {
            $('.qty').change(function () {
                var val = parseFloat(this.value);
                var row = $(this).closest('tr');
                var table = row.closest('table');
                var unit_price = calculateUnitPrice(table);

                table.find('.unit-price').val(unit_price.toFixed(2));
                updatePrices(table, unit_price);
            });

            function calculateUnitPrice(table) {
                var totalQty = 0;
                table.find('.qty').each(function(){
                    totalQty += parseFloat(this.value);
                });

                if (totalQty) {
                    return parseFloat(table.data('total')) / totalQty;
                }

                return 0;
            }

            function updatePrices(table, unit_price)
            {
                table.find('tbody tr').each(function(){
                    var _this = $(this);
                    var qty = parseFloat(_this.find('.qty').val());
                    _this.find('.total-price').text((qty * unit_price).toFixed(2));
                });
            }
        });
    </script>
@endsection