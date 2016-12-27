@extends('layouts.app')

@section('header')
    <h2>Import Actual Material &mdash; Status</h2>
@endsection

@section('body')

    {{Form::open()}}

    @foreach($resources as $activity => $activityResources)
        <article class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">{{$activity}}</h4>
            </div>

            <table class="table table-condensed table-bordered table-striped">
                <thead>
                <tr>
                    <th>Resource</th>
                    <th>Budget Unit</th>
                    <th>To date Qty</th>
                    <th>Progress</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($activityResources as $resource)
                    <tr>
                        <td>{{$resource->resource_name}}</td>
                        <td>{{number_format($resource->budget_unit, 2)}}</td>
                        <td>{{number_format($resource->to_date_qty, 2)}}</td>
                        <td>{{number_format($resource->progress, 1)}}%</td>
                        <td>
                            {{Form::select("status[{$resource->breakdown_resource_id}]", config('app.cost_status'), $resource->status ?: 'In Progress', ['class' => 'form-control input-sm'])}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </article>
    @endforeach

    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> Update</button>
    {{Form::close()}}

@endsection