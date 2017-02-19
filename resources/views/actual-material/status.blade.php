@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h4 class="pull-left">{{$project->name}} &mdash; Material &mdash; Status</h4>
        <h4 class="pull-right text-muted">#E06</h4>
    </div>
@endsection

@section('body')

    {{Form::open()}}

    @foreach($resources as $activity => $activityResources)
        <article class="panel panel-default activity-panel">
            <div class="panel-heading">
                <h4 class="panel-title ">{{$activity}}</h4>
                {{Form::select('', config('app.cost_status')->prepend('Select Status', ''), null, ['class' => 'form-control input-sm select-all'])}}
            </div>

            <table class="table table-condensed table-bordered table-striped">
                <thead>
                <tr>
                    <th>Cost Account</th>
                    <th>Description</th>
                    <th>Resource Code</th>
                    <th>Resource Name</th>
                    <th>Remark</th>
                    <th>Budget Unit</th>
                    <th>To date Qty</th>
                    <th>Progress</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($activityResources as $resource)
                    @php
                        $boq = \App\Boq::costAccountOnWbs($resource->wbs, $resource->cost_account)->first();
                        $resource->cost = $resource->cost()->first();
                    @endphp
                    <tr>
                        <td>{{$resource->cost_account}}</td>
                        <td>{{$boq->description}}</td>
                        <td>{{$resource->resource_code}}</td>
                        <td>{{$resource->resource_name}}</td>
                        <td>{{$resource->remarks}}</td>
                        <td>{{number_format($resource->budget_unit, 2)}}</td>
                        <td>{{number_format($resource->cost->to_date_qty, 2)}}</td>
                        <td>{{number_format($resource->progress, 1)}}%</td>
                        <td>
                            {{Form::select("status[{$resource->breakdown_resource_id}]", config('app.cost_status'), $resource->progress == 100? 'Closed' : $resource->status ?: 'In Progress', ['class' => 'form-control input-sm'])}}
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

@section('javascript')
    <script>
        $(function(){
            $('.select-all').on('change', function(){
                var value = $(this).val();
                if (value) {
                    $(this).closest('.panel').find('table').find('select').val(value);
                }
            });
        });
    </script>
    @endsection