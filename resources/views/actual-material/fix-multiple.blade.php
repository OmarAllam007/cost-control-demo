@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash;
            <small>Resources with multiple cost account</small>
        </h4>
        <h4 class="pull-right text-muted">#E04</h4>
    </div>
@endsection

@section('body')
    {{Form::open(['method' => 'post'])}}
    @foreach($resources as $activity => $activityData)
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activity}}</h4>
            </div>
        @php $counter = 0; @endphp
        @foreach($activityData as $resource)
            @php
                $totalQty = $resource['resources']->sum('budget_unit');
            @endphp
            @if ($counter > 0)
                <div class="panel-body"></div>
            @endif

                <table class="table table-bordered table-condensed table-hover table-striped" data-total-qty="{{$totalQty}}" data-qty="{{$resource[4]}}">
                    <thead>
                    <tr class="info">
                        <th class="text-center">&nbsp;</th>
                        <th>Activity</th>
                        <th>Description</th>
                        <th>Store Resource Code</th>
                        <th>Store Resource Name</th>
                        <th>Resource Code</th>
                        <th>Resource Name</th>
                        <th>Cost Account</th>
                        <th>Budget Unit</th>
                        <th>Remarks</th>
                        <th>Previous</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($resource['resources'] as $res)
                        @php
                            $boq = \App\Boq::costAccountOnWbs($res->wbs, $res->cost_account)->first();
                        @endphp
                        <tr data-budget="{{$res->budget_unit}}">
                            <td class="text-center">
                                {{Form::checkbox("resource[{$res->breakdown_resource_id}][included]", 1, true, ['class' => 'include'])}}
                            </td>
                            <td>{{$res->breakdown_resource->code}}</td>
                            <td>{{$boq->description ?? ''}}</td>
                            <td>{{$resource[7]}}</td>
                            <td>{{$resource[2]}}</td>
                            <td>{{$res->resource_code}}</td>
                            <td>{{$res->resource_name}}</td>
                            <td>{{$res->cost_account}}</td>
                            <td>{{$res->budget_unit}}</td>
                            <td>{{$res->remarks}}</td>
                            <td>{{number_format($res->qty_to_date, 2) }}</td>
                            <td>
                                {{Form::text("resource[{$res->breakdown_resource_id}][qty]", $qty = $totalQty? round($res->budget_unit * $resource[4]/$totalQty, 2) : 0, ['class' => 'form-control input-sm qty'])}}
                            </td>
                            <td class="unit-price-cell" data-value="{{$resource[5]}}">{{ number_format($resource[5], 2) }}</td>
                            <td class="total-cell" data-value="{{$amount = $qty * $resource[5]}}">{{ number_format($amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="totals-row">
                        <th th colspan="3">&nbsp;</th>
                        <th class="text-right">Store Qty</th>
                        <th class="original-qty" data-value="{{$resource[4]}}">{{ number_format($resource[4], 2) }}</th>
                        <th class="text-right">Store Total</th>
                        <th class="original-total" data-value="{{$resource[6]}}">{{ number_format($resource[6], 2) }}</th>

                        <th class="text-right">Qty</th>
                        <th class="total-qty-cell" data-value="{{$resource[4]}}">{{ number_format($resource[4], 2) }}</th>
                        <th class="text-right">Total</th>
                        <th colspan="4" class="total-amount-cell" data-value="{{$resource[6]}}">{{ number_format($resource[6], 2) }}</th>
                    </tr>
                    </tfoot>
                </table>
                @php $counter++; @endphp
        @endforeach
            </div>
    @endforeach

    <div class="form-group">
        <button class="btn btn-primary" id="submitBtn">Next <i class="fa fa-chevron-circle-right"></i></button>
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
                maximumFractionDigits: 4,
                minimumSignificantDigits: 2,
                maximumSignificantDigits: 4
            };

            $('.include').change(function () {
                var table = $(this).closest('table');
                recalculateQty(table);
            });

            $('.qty').change(function () {
                var _this = $(this);
                var row = _this.closest('tr');
                row.data('manual', true);

                var table = $(this).closest('table');
                recalculateQty(table);
                updateTotals(table);
            }).keydown(function (e) {
                if (e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 9) {
                    e.preventDefault();
                    var next = $(this).closest('tr').next().find('.qty');
                    if (next.length) {
                        next.focus();
                    }
                } else if (e.keyCode == 38) {
                    e.preventDefault();
                    var prev = $(this).closest('tr').prev().find('.qty');
                    if (prev.length) {
                        prev.focus();
                    }
                }
            }).first().focus();

            function recalculateQty(table) {
                var totalBudget = 0;
                var qty = table.data('qty');

                var rows = table.find('tbody tr').each(function () {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked') && !_this.data('manual')) {
                        totalBudget += _this.data('budget');
                    } else if (!_this.find('.include').prop('checked')) {
                        _this.data('manual', false).find('.qty').val('0.00').prop('readonly', true);
                    } else {
                        qty -= _this.find('.qty').val();
                    }
                });

                table.find('tbody tr').each(function () {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        if (!_this.data('manual')) {
                            var budget = _this.data('budget');
                            _this.find('.qty').val(((budget * qty / totalBudget) || 0).toFixed(4));
                        }
                        _this.find('.qty').prop('readonly', false);
                    } else {
                        _this.find('.qty').val('0.00').prop('readonly', true);
                    }
                });
            }

            function updateTotals(table) {
                var totalQty = 0, totalAmount = 0;
                table.find('tbody tr').each(function () {
                    var _this = $(this);
                    if (_this.find('.include').prop('checked')) {
                        var qty = parseFloat(_this.find('.qty').val()) || 0;
                        var unit_price = parseFloat(_this.find('.unit-price-cell').data('value'));
                        var total = qty * unit_price;
                        _this.find('.total-cell').data('value', total).text(parseFloat(total.toFixed(4)).toLocaleString(formatOptions));
                        totalQty += qty;
                        totalAmount += total;
                    }
                });

                table.find('.total-amount-cell').data('value', totalAmount).text(parseFloat(totalAmount).toLocaleString(formatOptions));
                table.find('.total-qty-cell').data('value', totalQty).text(parseFloat(totalQty).toLocaleString(formatOptions));

                var originalTotalAmount = parseFloat(table.find('.original-total').data('value'));
                var originalTotalQty = parseFloat(table.find('.original-qty').data('value'));

                if (cval(totalAmount) != cval(originalTotalAmount)) {
                    table.find('.total-amount-cell').addClass('text-danger');
                } else {
                    table.find('.total-amount-cell').removeClass('text-danger');
                }

                if (cval(totalQty) !== cval(originalTotalQty)) {
                    table.find('.total-qty-cell').addClass('text-danger');
                } else {
                    table.find('.total-qty-cell').removeClass('text-danger');
                }

                if (cval(totalAmount) == cval(originalTotalAmount) && cval(totalQty) == cval(originalTotalQty)) {
                    $('#submitBtn').prop('disabled', false).addClass('btn-success').removeClass('btn-danger');
                } else {
                    $('#submitBtn').prop('disabled', true).addClass('btn-danger').removeClass('btn-success');
                }
            }

            function cval(number) {
                number += 0.00005;
                return Math.floor(number * 10000);
            }
        });
    </script>
@endsection