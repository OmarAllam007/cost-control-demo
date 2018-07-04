@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            {{$project->name}} &mdash;
            Update Progress
        </h2>

        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back to project</a>
    </div>
@endsection

@section('body')

    @if (isset($activityProgress['failed']))
        <div class="alert alert-info display-flex">
            <p class="flex">
                <i class="fa fa-exclamation-triangle"></i> Failed to load some records.
            </p>

            
            <a href="{{url($activityProgress['failed'])}}" class="btn btn-warning"><i class="fa fa-download"></i> Download failed records</a>
        </div>
    @endif

    @if ($activityProgress['records']->count())
    <form action="" method="post">
        {{csrf_field()}}
        {{method_field("put")}}

        <div class="form-group text-right">
            <button class="btn btn-primary apply-all" type="button"><i class="fa fa-arrow-circle-down"></i> Update All</button>
        </div>
        
        @foreach ($activityProgress['records'] as $code => $activity)
            <article class="card">
                <h4 class="card-title">{{$activity->resources->first()->activity}} <small>({{$code}})</small></h4>

                <div class="card-body">
                    <table class="table table-striped table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th class="col-sm-3">Resource Code</th>
                                <th class="col-sm-4">Resource Name</th>
                                <th class="col-sm-2">Budget Cost</th>
                                <th class="col-sm-2">To Date Cost</th>
                                <th class="col-sm-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control text-right input-sm activity-progress" 
                                            value="{{old("activity_progress.{$code}", $activity->progress)}}" name="activity_progress[{{$code}}]">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm btn-default text-primary apply-activity" title="Apply to all resources" type="button">
                                                <i class="fa fa-arrow-circle-down"></i>
                                            </button>
                                        </span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($activity->resources as $resource)
                            <tr>
                                <td>{{$resource->resource_code}}</td>
                                <td>{{$resource->resource_name}}</td>
                                <td>{{number_format($resource->budget_cost, 2)}}</td>
                                <td>{{number_format($resource->to_date_cost, 2)}}</td>
                                <td {!!$errors->first("progress.{$resource->id}", 'class="has-error"')!!}>
                                    <input type="text" class="form-control text-right input-sm resource-progress" 
                                        value="{{old("progress.{$resource->id}", $resource->progress)}}" name="progress[{{$resource->id}}]">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                            
                    </table>
                </div>
            </article>
        @endforeach


        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
        </div>
    </form>
    @endif

@endsection

@section('javascript')
<script>
    $(function() {
        $('.apply-all').click(function(e) {
            e.preventDefault();

            $('.activity-progress').each(function() {
                $(this).closest('.card').find('.resource-progress').val(this.value);
            });
        });

        $('.apply-activity').click(function(e) {
            e.preventDefault();
            const card = $(this).closest('.card');
            let value = card.find('.activity-progress').val();
            card.find('.resource-progress').val(value);
        });

        const inputs = $('.form-control');
        inputs.on('keypress', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                _input = $(this);
                let next = 0;
                inputs.each(function(index, item) {
                    if (_input.is($(item))) {
                        next = index + 1;
                    }
                });

                if (next && inputs.get(next)) {
                    inputs.get(next).focus();
                }
                $(this).next('.form-control').focus();
            }
        });
    });
</script>
@endsection