@extends('layouts.iframe')

@section('body')
<div class="row">
    {{Form::model($breakdown, ['route' => ['breakdown.duplicate', $breakdown], 'class' => 'col-md-6 col-sm-9'])}}

    <div class="form-group {{$errors->first('wbs_level_id', 'has-error')}}">
        {{ Form::label('wbs_level_id', 'WBS Level', ['class' => 'control-label']) }}
        {{Form::hidden('wbs_level_id')}}
        <p>
            <a href="#WBSModal" data-toggle="modal" id="select-parent">
                {{($wbs_id = Form::getValueAttribute('wbs_level_id'))? App\WbsLevel::with('parent')->find($wbs_id)->path : 'Select WBS Level' }}
            </a>
        </p>
        {!! $errors->first('wbs_level_id', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group {{$errors->first('cost_account', 'has-errors')}}">
        {{Form::label('cost_account', 'Cost Account', ['class' => 'control-label'])}}
        {{Form::text('cost_account', null, ['class' => 'form-control', 'id' => 'CostAccount'])}}
    </div>

    @include('wbs-level._modal', ['value' => $wbs_id, 'input' => 'wbs_level_id', 'project_id' => $breakdown->project_id])

    <div class="form-group">
        <button type="submit" class="btn btn-primary"><i class="fa fa-copy"></i> Duplicate</button>
    </div>

    {{Form::close()}}
</div>

@endsection

@section('javascript')
    <script src="/js/tree-select.js"></script>
    <script src="/js/autocomplete.js"></script>
    <script>
        jQuery(function ($) {
            $('#CostAccount').completeList({
                url: '/api/cost-accounts?project={{request('project')}}'
            });
        });
    </script>
@stop