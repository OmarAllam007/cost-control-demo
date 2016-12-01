@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Material</h2>
@endsection

@section('body')
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
                            {{Form::text("resource[{$resource->breakdown_resource_id}]['qty']", round($resource->budget_qty * abs($line[10])/$totalQty, 2), ['class' => 'form-control input-sm qty'])}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
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
                        _this.find('.qty').val((budget * qty/totalBudget).toFixed(2));
                    } else {
                        _this.find('.qty').val('');
                    }
                });
            });
        });
    </script>
@endsection