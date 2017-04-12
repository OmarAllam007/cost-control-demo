<div class="row">
    <div class="col-md-6 col-sm-9">
        <div class="form-group form-group-sm {{ $errors->first('name', 'has-error') }}">
            {{ Form::label('name', null, ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('start_date', 'has-error') }}">
            {{ Form::label('start_date', null, ['class' => 'control-label']) }}
            {{ Form::date('start_date', $period->start_date ?? '', ['class' => 'form-control to-calendar']) }}
            {!! $errors->first('start_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm">
            <div class="checkbox">
                <label>
                    {{Form::checkbox('is_open')}}
                    Make this period active for this project
                    <small class="text-warning">(This will disable all periods in project)</small>
                </label>
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
        </div>        
    </div>
</div>