{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', null, ['class' => 'control-label','id'=>'name']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('project_code', 'has-error')}}">
            {{ Form::label('project_code', null, ['class' => 'control-label']) }}
            {{ Form::text('project_code', null, ['class' => 'form-control']) }}
            {!! $errors->first('project_code', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('client_name', 'has-error')}}">
            {{ Form::label('client_name', null, ['class' => 'control-label']) }}
            {{ Form::text('client_name', null, ['class' => 'form-control']) }}
            {!! $errors->first('client_name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('owner_id', 'has-error')}}">
            {{ Form::label('owner_id', 'Owner', ['class' => 'control-label']) }}
            {{ Form::select('owner_id', App\User::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('owner_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('cost_owner_id', 'has-error')}}">
            {{ Form::label('cost_owner_id', 'Cost Control Owner', ['class' => 'control-label']) }}
            {{ Form::select('cost_owner_id', App\User::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('cost_owner_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('cost_threshold', 'has-error')}}">
            {{ Form::label('cost_threshold', 'Cost Threshold', ['class' => 'control-label']) }}
            {{ Form::number('cost_threshold', null, ['class' => 'form-control', 'min' => 0, 'max' => 100]) }}
            {!! $errors->first('cost_threshold', '<div class="help-block">:message</div>') !!}
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