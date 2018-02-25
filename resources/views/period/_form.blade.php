<div class="row form-horizontal">
    <section class="col-sm-9">
        <h4>Financial Period</h4>

        <div class="form-group form-group-sm {{ $errors->first('global_period_id', 'has-error') }}">
            {{ Form::label('global_period_id', 'Global Period', ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::select('global_period_id', $globalPeriods, null, ['class' => 'form-control', 'placeholder' => '-- Select Period --']) }}
                {!! $errors->first('global_period_id', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('name', 'has-error') }}">
            {{ Form::label('name', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('name', null, ['class' => 'form-control']) }}
                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('start_date', 'has-error') }}">
            {{ Form::label('start_date', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::date('start_date', old('start_date', $period->start_date ?$period->start_date->format('Y-m-d'):''), ['class' => 'form-control to-calendar']) }}
                {!! $errors->first('start_date', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm">
            <div class="checkbox col-sm-9 col-sm-offset-3">
                <label>
                    {{Form::checkbox('is_open')}}
                    Make this period active for this project
                    <small class="text-warning">(This will disable all periods in project)</small>
                </label>
            </div>
        </div>

        <hr>

        <h4 class="section-header">Planning Info</h4>

        <article class="form-group {{$errors->first('planned_cost', 'has-error')}}">
            {{Form::label('planned_cost', null, ['class' => 'control-label col-sm-3'])}}
            <div class="col-sm-9">
                {{Form::number('planned_cost', null, ['class' => 'form-control'])}}
                {!! $errors->first('planned_cost', '<div class="help-block">:message</div>') !!}
            </div>
        </article>

        <article class="form-group {{$errors->first('earned_value', 'has-error')}}">
            {{Form::label('earned_value', null, ['class' => 'control-label col-sm-3'])}}
            <div class="col-sm-9">
                {{Form::number('earned_value', null, ['class' => 'form-control'])}}
                {!! $errors->first('earned_value', '<div class="help-block">:message</div>') !!}
            </div>
        </article>

        <article class="form-group {{$errors->first('actual_invoice_amount', 'has-error')}}">
            {{Form::label('actual_invoice_amount', null, ['class' => 'control-label col-sm-3'])}}
            <div class="col-sm-9">
                {{ Form::number('actual_invoice_amount', null, ['class' => 'form-control'])}}
                {!! $errors->first('actual_invoice_amount', '<div class="help-block">:message</div>') !!}
            </div>
        </article>

        <article class="form-group form-group-sm {{ $errors->first('planned_progress', 'has-error') }}">
            {{ Form::label('planned_progress', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('planned_progress', null, ['class' => 'form-control']) }}
                {!! $errors->first('planned_progress', '<div class="help-block">:message</div>') !!}
            </div>
        </article>

        <article class="form-group form-group-sm {{ $errors->first('planned_finish_date', 'has-error') }}">
            {{ Form::label('planned_finish_date', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::date('planned_finish_date', Carbon\Carbon::parse($period->project->expected_finished_date)->format('Y-m-d'), ['class' => 'form-control to-calendar']) }}
                {!! $errors->first('planned_finish_date', '<div class="help-block">:message</div>') !!}
            </div>
        </article>

        <hr>

        <h4>Actual Information</h4>
        <div class="form-group form-group-sm {{ $errors->first('spi_index', 'has-error') }}">
            {{ Form::label('spi_index', 'SPI Index', ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('spi_index', null, ['class' => 'form-control']) }}
                {!! $errors->first('spi_index', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('actual_progress', 'has-error') }}">
            {{ Form::label('actual_progress', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('actual_progress', null, ['class' => 'form-control']) }}
                {!! $errors->first('actual_progress', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('change_order_amount', 'has-error') }}">
            {{ Form::label('change_order_amount', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('change_order_amount', $period->project->change_order_amount ?? 0, ['class' => 'form-control']) }}
                {!! $errors->first('change_order_amount', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_extension', 'has-error') }}">
            {{ Form::label('time_extension', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('time_extension', null, ['class' => 'form-control']) }}
                {!! $errors->first('time_extension', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_elapsed', 'has-error') }}">
            {{ Form::label('time_elapsed', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('time_elapsed', null, ['class' => 'form-control']) }}
                {!! $errors->first('time_elapsed', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_remaining', 'has-error') }}">
            {{ Form::label('time_remaining', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('time_remaining', null, ['class' => 'form-control']) }}
                {!! $errors->first('time_remaining', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('time_remaining', 'has-error') }}">
            {{ Form::label('time_remaining', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('time_remaining', null, ['class' => 'form-control']) }}
                {!! $errors->first('time_remaining', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('expected_duration', 'has-error') }}">
            {{ Form::label('expected_duration', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('expected_duration', $period->project->expected_finished_date, ['class' => 'form-control']) }}
                {!! $errors->first('expected_duration', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group form-group-sm {{ $errors->first('duration_variance', 'has-error') }}">
            {{ Form::label('duration_variance', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::text('duration_variance', $period->duration_variance, ['class' => 'form-control']) }}
                {!! $errors->first('duration_variance', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <article class="form-group form-group-sm {{ $errors->first('forecast_finish_date', 'has-error') }}">
            {{ Form::label('forecast_finish_date', null, ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::date('forecast_finish_date', $period->forecast_finish_date? \Carbon\Carbon::parse($period->forecast_finish_date)->format('Y-m-d') : '', ['class' => 'form-control to-calendar']) }}
                {!! $errors->first('forecast_finish_date', '<div class="help-block">:message</div>') !!}
            </div>
        </article>

        <hr>

        <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
            </div>
        </div>

    </section>
</div>