{{method_field('PUT')}}
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

        <div class="form-group {{$errors->first('project_contract_value', 'has-error')}}">
            {{ Form::label('project_contract_value', null, ['class' => 'control-label']) }}
            {{ Form::text('project_contract_value', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_contract_value', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_start_date', 'has-error')}}">
            {{ Form::label('project_start_date', null, ['class' => 'control-label']) }}
            {{ Form::date('project_start_date', null, ['class' => 'form-control','contenteditable'=>'true']) }}
            {!! $errors->first('project_start_date', '<div class="help-block">:message</div>') !!}
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
</div>
