@extends('layouts.app')

@section('header')
<h2>{{$project->name}} &mdash; Material &mdash; Unit Mismatch</h2>
@endsection

@section('body')
{{Form::open(['method' => 'post'])}}

<table class="table table-bordered table-condensed table-hover table-striped">
    <thead>
        <tr>
            <th>Activity</th>
            <th>Resource Code</th>
            <th>Original Resource Name</th>
            <th>Target Resource Code</th>
            <th>Target Resource Name</th>
            <th>Cost Account</th>
            <th>Budget Qty</th>
            <th>Budget Cost</th>
            <th>Budget Unit</th>
            <th>Original U.O.M</th>
            <th>Target U.O.M</th>
            <th>Original Qty</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($units as $idx => $line)
        <tr data-total-price={{ abs($line[12]) }}>
            <td>
                {{$line['resource']->breakdown_resource->code}}
            </td>
            <td>
                {{$line[13]}}
            </td>
            <td>
                {{$line[8]}}
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
                {{ $line[9] }}
            </td>
            <td>
                {{ $line['resource']->measure_unit }}
            </td>
            <td>
                {{ abs($line[10]) }}
            </td>

            <td>
                {{Form::text("units[$idx][qty]", 0, ['class' => 'form-control input-sm qty'])}}
            </td>
            <td>
                {{Form::text("units[$idx][unit_price]", 0, ['class' => 'form-control input-sm unit-price', 'readonly'])}}
            </td>
            <td>
                {{ number_format(abs($line[12]), 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>



<div class="form-group">
<button class="btn btn-success">Next <i class="fa fa-chevron-circle-right"></i></button>
</div>

{{Form::close()}}

@endsection

@section('javascript')
<script>
    $(function () {
        $('.qty').change(function() {
            var val = this.value;
            var row = $(this).parents('tr');
            var total = row.data('total-price');
            if (val) {
                row.find('.unit-price').val((total/val).toFixed(2));
            } else {
                row.find('.unit-price').val(0);
            }
        });
    });
</script>
@endsection