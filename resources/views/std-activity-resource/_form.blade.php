{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('template_id', 'has-error')}}">
            {{ Form::label('template_id', 'Template', ['class' => 'control-label']) }}
            {{ Form::select('template_id', \App\BreakdownTemplate::options(), request()->template, ['class' => 'form-control']) }}
            {!! $errors->first('template_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('resource_id', 'has-error')}}">
            {{ Form::label('resource_id', 'Resource', ['class' => 'control-label']) }}
            {{ Form::select('resource_id', \App\Resources::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('resource_id', '<div class="help-block">:message</div>') !!}
        </div>


        <div class="form-group {{$errors->first('equation', 'has-error')}}">
            {{ Form::label('equation', 'Equation', ['class' => 'control-label']) }}
            <p class="text-info"><i class="fa fa-info-circle"></i> Please use <code>$v</code> for value in the equation</p>
            {{ Form::text('equation', null, ['class' => 'form-control']) }}
            {!! $errors->first('equation', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('labor_count', 'has-error')}}">
            {{ Form::label('labor_count', 'Labor Count', ['class' => 'control-label']) }}
            {{ Form::text('labor_count', null, ['class' => 'form-control']) }}
            {!! $errors->first('labor_count', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('productivity_id', 'has-error')}}">
            {{ Form::label('productivity_id', 'Productivity', ['class' => 'control-label']) }}
            {{ Form::select('productivity_id', \App\Productivity::options(), null, ['class' => 'form-control']) }}
            {!! $errors->first('productivity_id', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('remarks', 'has-error')}}">
            {{ Form::label('remarks', 'Remarks', ['class' => 'control-label']) }}
            {{ Form::text('remarks', null, ['class' => 'form-control']) }}
            {!! $errors->first('remarks', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </div>
</div>
