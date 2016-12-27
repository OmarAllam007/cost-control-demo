@extends('layouts.app')

@section('header')
    <h2>Improt Actual Material &mdash; Progresss</h2>
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
                <td>{{number_format($activityResource->budget_unit, 2)}}</td>
                <td>{{number_format($activityResource->to_date_qty, 2)}}</td>
                <td>
                    <div class="input-group">
                    {{Form::text("progress[{$activityResource->breakdown_resource_id}]", null, ['class' => 'form-control input-sm progress-val'])}}
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
                $(this).parents('table').find('.progress-val').val(this.value);
            });
        });
    </script>
@endsection