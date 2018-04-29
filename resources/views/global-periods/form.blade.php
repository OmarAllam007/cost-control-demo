<div class="row">
    <div class="col-md-9 col-sm-12 form-horizontal">

            <article class="form-group {{$errors->first('start_date', 'has-error')}}">
                {{Form::label('start_date', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::date('start_date', old('start_date', $global_period->start_date? $global_period->start_date->format('Y-m-d') : ''), ['class' => 'form-control'])}}
                    {!! $errors->first('start_date', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('end_date', 'has-error')}}">
                {{Form::label('end_date', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::date('end_date', old('end_date', $global_period->end_date? $global_period->end_date->format('Y-m-d') : ''), ['class' => 'form-control'])}}
                    {!! $errors->first('end_date', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

        @if ($global_period->exists)
        <fieldset>
            <legend>Dashboard Info</legend>

            <article class="form-group {{$errors->first('spi_index', 'has-error')}}">
                {{Form::label('spi_index', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::number('spi_index', null, ['class' => 'form-control', 'step' => '0.01'])}}
                    {!! $errors->first('spi_index', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('productivity_index', 'has-error')}}">
                {{Form::label('productivity_index', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::number('productivity_index', null, ['class' => 'form-control', 'step' => '0.01'])}}
                    {!! $errors->first('productivity_index', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('actual_progress', 'has-error')}}">
                {{Form::label('planned_progress', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    <div class="input-group">
                        {{Form::number('planned_progress', null, ['class' => 'form-control', 'step' => '0.01'])}}
                        <span class="input-group-addon">%</span>
                    </div>

                    {!! $errors->first('planned_progress', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('actual_progress', 'has-error')}}">
                {{Form::label('actual_progress', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    <div class="input-group">
                        {{Form::number('actual_progress', null, ['class' => 'form-control', 'step' => '0.01'])}}
                        <span class="input-group-addon">%</span>
                    </div>

                    {!! $errors->first('actual_progress', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('planned_value', 'has-error')}}">
                {{Form::label('planned_value', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::number('planned_value', null, ['class' => 'form-control', 'step' => '0.01'])}}
                    {!! $errors->first('planned_value', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('earned_value', 'has-error')}}">
                {{Form::label('earned_value', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::number('earned_value', null, ['class' => 'form-control', 'step' => '0.01'])}}
                    {!! $errors->first('earned_value', '<div class="help-block">:message</div>') !!}
                </div>
            </article>

            <article class="form-group {{$errors->first('actual_invoice_value', 'has-error')}}">
                {{Form::label('actual_invoice_value', null, ['class' => 'control-label col-sm-4'])}}
                <div class="col-sm-8">
                    {{Form::number('actual_invoice_value', null, ['class' => 'form-control', 'step' => '0.01'])}}
                    {!! $errors->first('actual_invoice_value', '<div class="help-block">:message</div>') !!}
                </div>
            </article>
        </fieldset>
        @endif

        <hr>

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-4">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>