@extends('layouts.app')

@section('header')
    <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash; Unit Mismatch</h4>
    <h4 class="pull-right text-muted">E#02</h4>
@endsection

@section('body')

    {{Form::open(['method' => 'post'])}}
    <table class="table table-bordered table-condensed table-hover table-striped">
        <thead>
        <tr>
            <th>Store Resource Code</th>
            <th>Store Resource Name</th>
            <th>Budget Resource Code</th>
            <th>Budget Resource Name</th>
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
        @foreach($units as $key => $line)
            <tr>
                <td>
                    {{$line[7]}}
                </td>
                <td>
                    {{$line[2]}}
                </td>
                <td>
                    {{$line['unit_resource']->resource_code}}
                </td>
                <td>
                    {{$line['unit_resource']->name}}
                </td>
                <td>
                    {{ $line[3] }}
                </td>
                <td>
                    {{ $line['unit_resource']->units->type }}
                </td>
                <td>
                    {{ number_format($line[4], 2) }}
                </td>

                <td>
                    {{number_format($line[5], 2)}}
                </td>

                <td>
                    {{Form::text("units[$key][qty]", 0, ['class' => 'form-control input-sm qty', 'tabindex' => $key + 1])}}
                </td>
                <td>
                    {{Form::text("units[$key][unit_price]", 0, ['class' => 'form-control input-sm unit-price', 'readonly', 'tabindex' => -1])}}
                </td>
                <td class="total-price" data-value="{{$line[6]}}">
                    {{ number_format($line[6], 2) }}
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
            $('.qty').change(function () {
                var val = parseFloat($(this).val());
                var parent = $(this).closest('tr');
                if (val) {
                    var total = parseFloat(parent.find('.total-price').data('value'));
                    var unit_price = total / val;
                    parent.find('.unit-price').val(unit_price.toFixed(2));
                } else {
                    parent.find('.unit-price').val('0.00')
                }
            }).keydown(function (e) {
                if (e.keyCode == 13 || e.keyCode == 40) {
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
            });
        });
    </script>
@endsection