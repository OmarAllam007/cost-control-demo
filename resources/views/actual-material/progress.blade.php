@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash; Progress</h4>
        <h4 class="pull-right text-muted">#E05</h4>
    </div>
@endsection

@section('body')
    {{ Form::open() }}
    @foreach($resources as $activityName => $activityResources)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">{{$activityName}}</h4>
        </div>

        <table class="table table-bordered table-condensed table-striped">
            <thead>
            <tr>
                <th>Resource Code</th>
                <th>Resource Name</th>
                <th>Budget Unit</th>
                <th>To date Qty</th>
                <th>Remaining</th>
                <th>
                    <label style="display: block;">
                        Progress
                        <div class="input-group">
                            <input type="text" class="form-control input-sm activity-progress">
                            <span class="input-group-addon">%</span>
                        </div>
                    </label>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($activityResources as $activityResource)
            <tr>
                <td>{{$activityResource->resource_code}}</td>
                <td>{{$activityResource->resource_name}}</td>
                <td class="budget-cell" data-value="{{$activityResource->budget_unit}}">{{number_format($activityResource->budget_unit, 2)}}</td>
                <td class="todate-cell" data-value="{{$activityResource->cost->to_date_qty}}">{{number_format($activityResource->cost->to_date_qty, 2)}}</td>
                <td class="remaining-cell">{!! $activityResource->cost->remaining_qty < 0 ? '<span class="text-danger">' . number_format($activityResource->cost->remaining_qty, 2) . '</span>' : number_format($activityResource->remaining_qty, 2) !!}</td>
                <td>
                    <div class="input-group">
                    {{Form::text("progress[{$activityResource->breakdown_resource_id}]", $activityResource->progress, ['class' => 'form-control input-sm progress-val'])}}
                    <span class="input-group-addon">%</span>
                    </div>
                    {!! $errors->first("progress.{$activityResource->breakdown_resource_id}", '<div class="help-block"><span class="text-danger">:message</span></div>') !!}
                </td>
            </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <button class="btn btn-success">Next <i class="fa fa-chevron-circle-right"></i></button>
    {{ Form::close() }}
@endsection

@section('javascript')
    <script>
        $(function(){
            $('.activity-progress').on('change', function() {
                $(this).closest('table').find('.progress-val').val(this.value).change();
            });

            $('.progress-val').on('change', function(){
                var parent = $(this).closest('tr');
                var remainingCell = parent.find('.remaining-cell');
                var remaining = 0;

                if (this.value >= 100) {
                    this.value = 100;
                    remaining = 0;
                } else if (this.value <= 0) {
                    this.value = 0;
                    remaining = parent.find('.budget-cell').data('value');
                } else {
                    var progress_value = this.value / 100;
                    var todate_value = parent.find('.todate-cell').data('value');
                    remaining = todate_value * (1 - progress_value) / progress_value;
                }

                var remaining_value = parseFloat(remaining.toFixed(2)).toLocaleString({style: 'currency'});
                console.log(remaining_value);
                if (remaining < 0) {
                    remaining_value = '<strong class="text-danger">' + remaining_value + '</strong>';
                }
                remainingCell.html(remaining_value);
            });

        });
    </script>
@endsection
