@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Material</h2>
@endsection

@section('body')
{{ dump($multiple) }}
    {{Form::open(['method' => 'post'])}}
    @foreach($multiple as $line)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">{{$line[3]}}</h4>
            </div>

            <table class="table table-bordered table-condensed table-hover table-striped"
                   data-total-qty="{{$totalQty = $line['resources']->sum('budget_qty')}}" data-qty="{{abs($line[10])}}">
                <thead>
                <tr>
                    <th class="text-center">&nbsp;</th>
                    <th>Activity</th>
                    <th>Resource Code</th>
                    <th>Resource Name</th>
                    <th>Original Resource Name</th>
                    <th>Cost Account</th>
                    <th>Budget Qty</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($line['resources'] as $resource)
                    <tr data-budget="{{$resource->budget_qty}}">
                        <td class="text-center">
                            {{Form::checkbox("resource[{$resource->breakdown_resource_id}]['included']", 1, true, ['class' => 'include'])}}
                        </td>
                        <td>
                            {{$resource->breakdown_resource->code}}
                        </td>
                        <td>
                            {{$resource->resource_code}}
                        </td>
                        <td>
                            {{$resource->resource_name}}
                        </td>
                        <td>
                            {{$line[8]}}
                        </td>
                        <td>
                            {{$resource->cost_account}}
                        </td>
                        <td>
                            {{$resource->budget_qty}}
                        </td>
                        <td>
                            {{Form::text("resource[{$resource->breakdown_resource_id}]['qty']", $qty = round($resource->budget_qty * abs($line[10])/$totalQty, 2), ['class' => 'form-control input-sm qty'])}}
                        </td>
                        <td class="unit-price-cell">{{ number_format($line[11], 2) }}</td>
                        <td class="total-cell">{{ number_format($qty * $line[11], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="totals-row">
                        <th th colspan="2">&nbsp;</th>
                        <th class="text-right">Original Qty</th>
                        <th class="total-qty-cell">{{ number_format(abs($line[10]), 2) }}</th>
                        <th class="text-right">Original Total</th>
                        <th class="total-amount-cell">{{ number_format(abs($line[12]), 2) }}</th>

                        <th class="text-right">Qty</th>
                        <th class="total-qty-cell">{{ number_format(abs($line[10]), 2) }}</th>
                        <th class="text-right">Total</th>
                        <th class="total-amount-cell">{{ number_format(abs($line[12]), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    <div class="form-group">
        <button class="btn btn-success"><i class="fa fa-check"></i> Update</button>
    </div>

    {{Form::close()}}

@endsection

@section('javascript')
    <script>
        $(function () {
            var formatOptions = {style: 'currency', currency: 'SAR', minimumFractionDigits: 2, maximumFractionDigits: 2, minimumSignificantDigits: 2, maximumSignificantDigits: 2};

            $('.include').change(function () {
                var table = $(this).parents('table');
                var totalBudget = 0;
                var qty = table.data('qty');
                table.find('tbody tr').each(function(){
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        totalBudget += _this.data('budget');
                    }
                }).each(function() {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        var budget = _this.data('budget');
                        _this.find('.qty').val((budget * qty/totalBudget).toFixed(2)).prop('readonly', false).change();
                    } else {
                        _this.find('.qty').val('').prop('readonly', true).change();
                    }
                });
            });

            $('.qty').change(function(){
                var _this = $(this);
                var row = _this.parents('tr');
                if (!row.find('.include').prop('checked')) {
                    row.find('.total-cell').text('0.00');
                }
                var value = this.value || 0;
                var unit_price = parseFloat(row.find('.unit-price-cell').text());
                var total = parseFloat(value * unit_price);
                row.find('.total-cell').text(total.toFixed(2).toLocaleString(formatOptions));

                updateTotals($(this).parents('table'));
            });

            function updateTotals(table)
            {
                var totalQty = 0;
                var totalQty = 0, totalAmount = 0;
                table.find('tbody tr').each(function(){
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        totalQty += parseFloat(_this.find('.qty').val()) || 0;
                        totalAmount += parseFloat(_this.find('.total-cell').text()) || 0;
                    }
                });

                table.find('.total-amount-cell').text(totalAmount.toLocaleString(formatOptions));
                table.find('.total-qty-cell').text(totalQty.toLocaleString(formatOptions));
            }
        });
    </script>
@endsection