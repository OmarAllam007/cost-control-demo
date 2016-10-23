@extends('layouts/iframe')

@section('body')
    {{ Form::model($breakdown_resource, ['route' => ['breakdown-resource.update', $breakdown_resource], 'method' => 'patch', 'class' => 'col-sm-9']) }}

    <div class="form-group {{$errors->first('std_activity_id', 'has-error')}}">
        {{Form::label('wbs_level_id', 'WBS Level', ['class' => 'control-label'])}}

        <p>
            <a href="#WBSModal" data-toggle="modal">
                <em>{{($wbs_id = Form::getValueAttribute('wbs_level_id'))? \App\WbsLevel::find($wbs_id)->path : 'Select WBS Level'}}</em>
            </a>
        </p>
        {!! $errors->first('wbs_level_id', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group {{$errors->first('std_activity_id', 'has-error')}}">
        {{Form::label('activity', null, ['class' => 'control-label'])}}
        <p>
            <a href="#ActivitiesModal" data-toggle="modal">
                <em>{{($activity_id = Form::getValueAttribute('std_activity_id'))? \App\StdActivity::find($activity_id)->name : 'Select Activity'}}</em>
            </a>
        </p>
        {!! $errors->first('std_activity_id', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group {{$errors->first('cost_account', 'has-error')}}">
        {{Form::label('cost_account', null, ['class' => 'control-label'])}}
        {{Form::text('cost_account', null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
        {!! $errors->first('cost_account', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group {{$errors->first('resource_qty', 'has-error')}}">
        {{Form::label('resource_qty', "Resource Quantity", ['class' => 'control-label'])}}
        {{Form::text('resource_qty', null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
        {!! $errors->first('resource_qty', '<div class="help-block">:message</div>') !!}
    </div>


    <div class="form-group {{$errors->first('labor_count', 'has-error')}}">
        {{Form::label('labor_count', null, ['class' => 'control-label'])}}
        {{Form::text('labor_count', null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
        {!! $errors->first('labor_count', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group {{$errors->first('productivity_id', 'has-error')}}">
        {{Form::label('productivity_id', "Productivity Reference", ['class' => 'control-label'])}}
        {{Form::select('productivity_id', App\Productivity::options(), null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
        {!! $errors->first('productivity_id', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group">
        <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
    </div>

    @include('std-activity._modal', ['value' => $activity_id])
    @include('wbs-level._modal', ['value' => $wbs_id, 'project_id' => $breakdown_resource->breakdown->project_id])

    {{ Form::close() }}
@endsection

@section('javascript')
    <script src="{{asset('/js/autocomplete.js')}}"></script>
    <script>
        jQuery(function ($) {
            $('#CostAccount').completeList({
                url: '/api/cost-accounts?project={{$breakdown_resource->breakdown->project_id}}'
            });
        });
    </script>
@endsection