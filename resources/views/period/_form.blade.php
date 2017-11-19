<div class="row">
    <section class="col-sm-6">
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
    </section>

    <section class="col-sm-6">
        <h4>Project Information</h4>
        <div class="form-group form-group-sm {{ $errors->first('spi_index', 'has-error') }}">
            {{ Form::label('spi_index', 'SPI Index', ['class' => 'control-label']) }}
            {{ Form::text('spi_index', $period->spi_index ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('spi_index', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('planned_progress', 'has-error') }}">
            {{ Form::label('planned_progress', null, ['class' => 'control-label']) }}
            {{ Form::text('planned_progress', $period->planned_progress ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('planned_progress', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('actual_progress', 'has-error') }}">
            {{ Form::label('actual_progress', null, ['class' => 'control-label']) }}
            {{ Form::text('actual_progress', $period->actual_progress ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('actual_progress', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('change_order_amount', 'has-error') }}">
            {{ Form::label('change_order_amount', null, ['class' => 'control-label']) }}
            {{ Form::text('change_order_amount', $period->change_order_amount ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('change_order_amount', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_extension', 'has-error') }}">
            {{ Form::label('time_extension', null, ['class' => 'control-label']) }}
            {{ Form::text('time_extension', $period->time_extension ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('time_extension', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('planned_finish_date', 'has-error') }}">
            {{ Form::label('planned_finish_date', null, ['class' => 'control-label']) }}
            {{ Form::text('planned_finish_date', $period->planned_finish_date ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('planned_finish_date', '<div class="help-block">:message</div>') !!}
        </div>
    </section>
</div>

<div class="form-group">
    <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
</div>