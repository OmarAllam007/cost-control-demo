@extends('layouts.app')

@section('header')
    <h4>Material &mdash; {{$project->name}} &mdash; Closed Resources</h4>
    <h4 class="pull-right text-muted">#E03</h4>
@endsection

@section('body')
    {{Form::open()}}
    @foreach($closed as $activity => $resources)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activity}}</h4>
            </div>

            <table class="table table-condensed table-bordered table-striped">
                <thead>
                <tr>
                    <th>Resource Code</th>
                    <th>Resource Name</th>
                    <th>Remarks</th>
                    <th>Budget Unit</th>
                    <th>To Date Qty</th>
                    <th>U.O.M</th>
                    <th class="text-center">In progress</th>
                    <th class="text-center">Progress</th>
                </tr>
                </thead>

                <tbody>
                @foreach($resources as $resource)
                    <tr>
                        <td>{{$resource->resource_code}}</td>
                        <td>{{$resource->resource_name}}</td>
                        <td>{{$resource->remarks}}</td>
                        <td>{{number_format($resource->budget_unit, 2)}}</td>
                        <td>{{number_format($resource->qty_to_date ?? 0, 2)}}</td>
                        <td>{{$resource->measure_unit}}</td>
                        <td class="text-center">
                            <label style="display: block; margin: 0">
                                {{Form::hidden("closed[{$resource->id}][open]", 0)}}
                                {{Form::checkbox("closed[{$resource->id}][open]", 1, null, ['class' => 'is-open'])}}
                            </label>
                        </td>
                        <td class="text-center">
                            {{Form::text("closed[{$resource->id}][progress]", null, ['class' => 'form-control input-sm progress-value'])}}
                        </td>
                    </tr>
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
            $('.is-open').on('change', function () {
                var input = $(this).closest('tr').find('.progress-value').attr('disabled', !this.checked);
                if (this.checked) {
                    input.val(99).focus();
                    input.get(0).selectionStart = 0;
                    input.get(0).selectionEnd = 2;
                } else {
                    input.val('');
                }
            }).change();
        });
    </script>
@endsection