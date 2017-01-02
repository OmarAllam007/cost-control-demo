@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash; <small>Resources with multiple cost account</small></h4>
        <h4 class="pull-right text-muted">#E03</h4>
    </div>
@endsection

@section('body')
    {{Form::open(['method' => 'post'])}}
    @foreach($multiple as $activityCode => $activity)
        @foreach($activity as $resourceCode => $resource)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{$resource['resources']->first()->wbs->name}}
                        / {{$resource['resources']->first()->activity }}</h4>
                </div>

                <table class="table table-bordered table-condensed table-hover table-striped"
                       data-total-qty="{{$totalQty = $resource['resources']->sum('budget_unit')}}"
                       data-qty="{{abs($resource[4])}}">
                    <thead>
                    <tr>
                        <th class="text-center">&nbsp;</th>
                        <th>Activity</th>
                        <th>Store Resource Code</th>
                        <th>Store Resource Name</th>
                        <th>Resource Code</th>
                        <th>Resource Name</th>
                        <th>Cost Account</th>
                        <th>Budget Unit</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Remarks</th>
                        <th>Previous</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($resource['resources'] as $res)
                        <tr data-budget="{{$res->budget_unit}}">
                            <td class="text-center">
                                {{Form::checkbox("resource[$activityCode][$resourceCode][{$res->breakdown_resource_id}][included]", 1, true, ['class' => 'include'])}}
                            </td>
                            <td>
                                {{$res->breakdown_resource->code}}
                            </td>
                            <td>
                                {{$resource[7]}}
                            </td>
                            <td>
                                {{$resource[3]}}
                            </td>
                            <td>
                                {{$res->resource_code}}
                            </td>
                            <td>
                                {{$res->resource_name}}
                            </td>
                            <td>
                                {{$res->cost_account}}
                            </td>
                            <td>
                                {{$res->budget_unit}}
                            </td>
                            <td>
                                {{Form::text("resource[$activityCode][$resourceCode][{$res->breakdown_resource_id}][qty]", $qty = round($res->budget_unit * abs($resource[4])/$totalQty, 2), ['class' => 'form-control input-sm qty'])}}
                            </td>
                            <td class="unit-price-cell">{{ number_format($resource[5], 2) }}</td>
                            <td class="total-cell">{{ number_format($qty * $resource[5], 2) }}</td>
                            <td>
                                {{$res->remarks}}
                            </td>
                            <td>
                                {{$res->cost ? number_format($res->cost->previous_qty, 2) : 0 }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="totals-row">
                        <th th colspan="3">&nbsp;</th>
                        <th class="text-right">Store Qty</th>
                        <th class="original-qty">{{ number_format(abs($resource[4]), 2) }}</th>
                        <th class="text-right">Store Total</th>
                        <th class="original-total">{{ number_format(abs($resource[6]), 2) }}</th>

                        <th class="text-right">Qty</th>
                        <th class="total-qty-cell">{{ number_format(abs($resource[4]), 2) }}</th>
                        <th class="text-right">Total</th>
                        <th colspan="3" class="total-amount-cell">{{ number_format(abs($resource[6]), 2) }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    @endforeach

    <div class="form-group">
        <button class="btn btn-success" id="submitBtn">Next <i class="fa fa-chevron-circle-right"></i></button>
    </div>

    {{Form::close()}}
@endsection

@section('javascript')
    <script>
        $(function () {
            var formatOptions = {
                style: 'currency',
                currency: 'SAR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                minimumSignificantDigits: 2,
                maximumSignificantDigits: 2
            };

            $('.include').change(function () {
                var table = $(this).parents('table');


                recalculateQty(table);
            });

            $('.qty').change(function () {
                var _this = $(this);
                var row = _this.parents('tr');
                row.data('manual', true);
                updateUnitPrice(row);
                var table = $(this).parents('table');
                recalculateQty(table);
                updateTotals(table);
            }).keydown(function (e) {
                if (e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 9) {
                    e.preventDefault();
                    var next = $(this).parents('tr').next().find('.qty');
                    if (next.length) {
                        next.focus();
                    }
                } else if (e.keyCode == 38) {
                    e.preventDefault();
                    var prev = $(this).parents('tr').prev().find('.qty');
                    if (prev.length) {
                        prev.focus();
                    }
                }
            }).first().focus();

            function updateUnitPrice(row) {
                var value = parseFloat(row.find('.qty').val()) || 0;
                var unit_price = parseFloat(row.find('.unit-price-cell').text());
                var total = parseFloat(value * unit_price);
                row.find('.total-cell').text(total.toFixed(2).toLocaleString(formatOptions));
            }

            function recalculateQty(table) {
                var totalBudget = 0;
                var qty = table.data('qty');

                var rows = table.find('tbody tr').each(function () {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked') && !_this.data('manual')) {
                        totalBudget += _this.data('budget');
                    } else if (!_this.find('.include').prop('checked'))  {
                        _this.data('manual', false).find('.qty').val('0.00').prop('readonly', true);
                        updateUnitPrice(_this);
                    } else {
                        qty -= _this.find('.qty').val();
                    }
                });

                table.find('tbody tr').each(function () {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        if (!_this.data('manual')) {
                            var budget = _this.data('budget');
                            _this.find('.qty').val((budget * qty / totalBudget).toFixed(2));
                            updateUnitPrice(_this);
                        }
                        _this.find('.qty').prop('readonly', false);
                    } else {
                        _this.find('.qty').val('0.00').prop('readonly', true);
                        updateUnitPrice(_this);
                    }
                });
            }

            function updateTotals(table) {
                var totalQty = 0;
                var totalQty = 0, totalAmount = 0;
                table.find('tbody tr').each(function () {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        totalQty += parseFloat(_this.find('.qty').val()) || 0;
                        totalAmount += parseFloat(_this.find('.total-cell').text()) || 0;
                    }
                });

                table.find('.total-amount-cell').text(parseFloat(totalAmount.toFixed(2)).toLocaleString(formatOptions));
                table.find('.total-qty-cell').text(parseFloat(totalQty.toFixed(2)).toLocaleString(formatOptions));

                var originalTotalAmount = parseFloat($('.original-total').text());
                var originalTotalQty = parseFloat($('.original-qty').text());

                if (totalAmount != originalTotalAmount) {
                    table.find('.total-amount-cell').addClass('text-danger');
                } else {
                    table.find('.total-amount-cell').removeClass('text-danger');
                }

                if (totalQty != originalTotalQty) {
                    table.find('.total-qty-cell').addClass('text-danger');
                } else {
                    table.find('.total-qty-cell').removeClass('text-danger');
                }

                if (totalAmount == originalTotalAmount && totalQty == originalTotalQty) {
                    $('#submitBtn').prop('disabled', false).addClass('btn-success').removeClass('btn-danger');
                } else {
                    $('#submitBtn').prop('disabled', true).addClass('btn-danger').removeClass('btn-success');
                }
            }
        });
    </script>
@endsection