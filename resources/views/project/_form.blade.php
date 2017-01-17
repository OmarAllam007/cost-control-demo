{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', null, ['class' => 'control-label','id'=>'name']) }}
            {{ Form::text('name', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_code', 'has-error')}}">
            {{ Form::label('project_code', null, ['class' => 'control-label']) }}
            {{ Form::text('project_code', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_code', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('owner_id', 'has-error')}}">
            {{ Form::label('owner_id', 'Owner', ['class' => 'control-label']) }}
            {{ Form::select('owner_id', App\User::options(), old('owner_id', $project->owner_id ?: Auth::user()->id), ['class' => 'form-control']) }}
            {!! $errors->first('owner_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('cost_owner_id', 'has-error')}}">
            {{ Form::label('cost_owner_id', 'Cost Control Owner', ['class' => 'control-label']) }}
            {{ Form::select('cost_owner_id', App\User::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('cost_owner_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('client_name', 'has-error')}}">
            {{ Form::label('client_name', null, ['class' => 'control-label']) }}
            {{ Form::text('client_name', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('client_name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_location', 'has-error')}}">
            {{ Form::label('project_location', null, ['class' => 'control-label']) }}
            {{ Form::text('project_location', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_location', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_contract_budget_value', 'has-error')}}">
            {{ Form::label('project_contract_budget_value', null, ['class' => 'control-label']) }}
            {{ Form::text('project_contract_budget_value', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_contract_budget_value', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_contract_signed_value', 'has-error')}}">
            {{ Form::label('project_contract_signed_value', null, ['class' => 'control-label']) }}
            {{ Form::text('project_contract_signed_value', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_contract_signed_value', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_start_date', 'has-error')}}">
            {{ Form::label('project_start_date', null, ['class' => 'control-label']) }}
            {{ Form::date('project_start_date', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_start_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('original_finished_date', 'has-error')}}">
            {{ Form::label('original_finished_date', null, ['class' => 'control-label']) }}
            {{ Form::date('original_finished_date', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('original_finished_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('expected_finished_date', 'has-error')}}">
            {{ Form::label('expected_finished_date', null, ['class' => 'control-label']) }}
            {{ Form::date('expected_finished_date', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('expected_finished_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('change_order_amount', 'has-error')}}">
            {{ Form::label('change_order_amount', null, ['class' => 'control-label']) }}
            {{ Form::text('change_order_amount', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('change_order_amount', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('direct_cost_material', 'has-error')}}">
            {{ Form::label('direct_cost_material', null, ['class' => 'control-label']) }}
            {{ Form::text('direct_cost_material', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('direct_cost_material', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('indirect_cost_general', 'has-error')}}">
            {{ Form::label('indirect_cost_general', null, ['class' => 'control-label']) }}
            {{ Form::text('indirect_cost_general', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('indirect_cost_general', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('total_budget_cost', 'has-error')}}">
            {{ Form::label('total_budget_cost', null, ['class' => 'control-label']) }}
            {{ Form::text('total_budget_cost', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('total_budget_cost', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_duration', 'has-error')}}">
            {{ Form::label('project_duration', null, ['class' => 'control-label']) }}
            {{ Form::text('project_duration', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_duration', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{ Form::label('description', 'Description', ['class' => 'control-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>




        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>

    <div class="col-sm-6">
        @include('project.permissions')
    </div>
</div>

@section('javascript')
    <script src="{{asset('/js/project-permissions.js')}}"></script>
@endsection