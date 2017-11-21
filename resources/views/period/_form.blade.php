<div class="row">
    <section class="col-sm-6">
        <h4>Financial Period</h4>
        <div class="form-group form-group-sm {{ $errors->first('name', 'has-error') }}">
            {{ Form::label('name', null, ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('start_date', 'has-error') }}">
            {{ Form::label('start_date', null, ['class' => 'control-label']) }}
            {{ Form::date('start_date', null, ['class' => 'form-control to-calendar']) }}
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
            {{ Form::text('spi_index', null, ['class' => 'form-control']) }}
            {!! $errors->first('spi_index', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('planned_progress', 'has-error') }}">
            {{ Form::label('planned_progress', null, ['class' => 'control-label']) }}
            {{ Form::text('planned_progress', null, ['class' => 'form-control']) }}
            {!! $errors->first('planned_progress', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('actual_progress', 'has-error') }}">
            {{ Form::label('actual_progress', null, ['class' => 'control-label']) }}
            {{ Form::text('actual_progress', null, ['class' => 'form-control']) }}
            {!! $errors->first('actual_progress', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('change_order_amount', 'has-error') }}">
            {{ Form::label('change_order_amount', null, ['class' => 'control-label']) }}
            {{ Form::text('change_order_amount', $project->change_order_amount ?? '', ['class' => 'form-control']) }}
            {!! $errors->first('change_order_amount', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_extension', 'has-error') }}">
            {{ Form::label('time_extension', null, ['class' => 'control-label']) }}
            {{ Form::text('time_extension', null, ['class' => 'form-control']) }}
            {!! $errors->first('time_extension', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('planned_finish_date', 'has-error') }}">
            {{ Form::label('planned_finish_date', null, ['class' => 'control-label']) }}
            {{ Form::text('planned_finish_date', $period->project->expected_finished_date, ['class' => 'form-control']) }}
            {!! $errors->first('planned_finish_date', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_elapsed', 'has-error') }}">
            {{ Form::label('time_elapsed', null, ['class' => 'control-label']) }}
            {{ Form::text('time_elapsed', $period->project->expected_finished_date, ['class' => 'form-control']) }}
            {!! $errors->first('time_elapsed', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_remaining', 'has-error') }}">
            {{ Form::label('time_remaining', null, ['class' => 'control-label']) }}
            {{ Form::text('time_remaining', $period->project->expected_finished_date, ['class' => 'form-control']) }}
            {!! $errors->first('time_remaining', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('expected_duration', 'has-error') }}">
            {{ Form::label('expected_duration', null, ['class' => 'control-label']) }}
            {{ Form::text('expected_duration', $period->project->expected_finished_date, ['class' => 'form-control']) }}
            {!! $errors->first('expected_duration', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group form-group-sm {{ $errors->first('duration_variance', 'has-error') }}">
            {{ Form::label('duration_variance', null, ['class' => 'control-label']) }}
            {{ Form::text('duration_variance', $period->project->expected_finished_date, ['class' => 'form-control']) }}
            {!! $errors->first('duration_variance', '<div class="help-block">:message</div>') !!}
        </div>
    </section>
</div>

<div class="form-group">
    <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
</div>