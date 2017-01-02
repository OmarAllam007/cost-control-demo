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
            <table class="table table-bordered table-condensed table-hover table-striped">
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
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $idx => $line)
                    <tr data-total-price={{ abs($line[6]) }}>
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
                            {{ abs($line[4]) }}
                        </td>

                        <td>
                            {{Form::text("units[{$line['resource']->breakdown_resource_id}][qty]", 0, ['class' => 'form-control input-sm qty', 'tabindex' => $idx])}}
                        </td>
                        <td>
                            {{Form::text("units[{$line['resource']->breakdown_resource_id}][unit_price]", 0, ['class' => 'form-control input-sm unit-price', 'readonly', 'tabindex' => -1])}}
                        </td>
                        <td>
                            {{ number_format(abs($line[6]), 2) }}
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
                var val = this.value;
                var row = $(this).parents('tr');
                var total = row.data('total-price');
                if (val) {
                    row.find('.unit-price').val((total / val).toFixed(2));
                } else {
                    row.find('.unit-price').val(0);
                }
            });
        });
    </script>
@endsection