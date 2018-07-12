<div class="row form-horizontal">
    <section class="col-sm-9">

        <div class="form-group form-group-sm {{ $errors->first('global_period_id', 'has-error') }}">
            {{ Form::label('global_period_id', 'Global Period', ['class' => 'control-label col-sm-3']) }}
            <div class="col-sm-9">
                {{ Form::select('global_period_id', $globalPeriods, null, ['class' => 'form-control', 'placeholder' => '-- Select Period --']) }}
                {!! $errors->first('global_period_id', '<div class="help-block">:message</div>') !!}
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

        @if ($period->exists)

            <fieldset>
                <legend>Revised Contract</legend>

                <div class="form-group form-group-sm {{ $errors->first('change_order_amount', 'has-error') }}">
                    {{ Form::label('change_order_amount', 'Total Change Order Amount', ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        {{ Form::text('change_order_amount', null, ['class' => 'form-control']) }}
                        {!! $errors->first('change_order_amount', '<div class="help-block">:message</div>') !!}
                    </div>
                </div>

                <div class="form-group form-group-sm {{ $errors->first('potential_change_order_amount', 'has-error') }}">
                    {{ Form::label('potential_change_order_amount', 'Potential Change Order Amount', ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        {{ Form::text('potential_change_order_amount', null, ['class' => 'form-control']) }}
                        {!! $errors->first('potential_change_order_amount', '<div class="help-block">:message</div>') !!}
                    </div>
                </div>

                {{--  <div class="form-group form-group-sm {{ $errors->first('time_extension', 'has-error') }}">
                      {{ Form::label('time_extension', "Total Time Extension", ['class' => 'control-label col-sm-3']) }}
                      <div class="col-sm-9">
                          <div class="input-group">
                              {{ Form::text('time_extension', null, ['class' => 'form-control', 'disabled']) }}
                              <span class="input-group-addon">Days</span>
                          </div>
                          {!! $errors->first('time_extension', '<div class="help-block">:message</div>') !!}
                      </div>
                  </div>
                      <div class="form-group form-group-sm {{ $errors->first('time_extension', 'has-error') }}">
                          {{ Form::label('time_extension', "Total Time Extension", ['class' => 'control-label col-sm-3']) }}
                          <div class="col-sm-9">
                              <div class="input-group">
                                  {{ Form::text('time_extension', null, ['class' => 'form-control', 'disabled']) }}
                                  <span class="input-group-addon">Days</span>
                              </div>
                              {!! $errors->first('time_extension', '<div class="help-block">:message</div>') !!}
                          </div>
                      </div>

                      <div class="form-group form-group-sm {{ $errors->first('expected_duration', 'has-error') }}">
                          {{ Form::label('expected_duration', 'Total duration', ['class' => 'control-label col-sm-3']) }}
                          <div class="col-sm-9">
                              <div class="input-group">
                                  {{ Form::text('expected_duration', $period->project_duration, ['class' => 'form-control', 'disabled']) }}
                                  <span class="input-group-addon">Days</span>
                              </div>
                              {!! $errors->first('expected_duration', '<div class="help-block">:message</div>') !!}
                          </div>
                      </div>--}}


                {{--change_order_amount--}}
            </fieldset>
            <fieldset>
                <legend>Planning Info</legend>
                <article class="form-group form-group-sm {{ $errors->first('planned_progress', 'has-error') }}">
                    {{ Form::label('planned_progress', null, ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        <div class="input-group">
                            {{ Form::text('planned_progress', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">%</span>
                        </div>
                        {!! $errors->first('planned_progress', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>
                <div class="form-group form-group-sm {{ $errors->first('actual_progress', 'has-error') }}">
                    {{ Form::label('actual_progress', null, ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        <div class="input-group">
                            {{ Form::text('actual_progress', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">%</span>
                        </div>
                        {!! $errors->first('actual_progress', '<div class="help-block">:message</div>') !!}
                    </div>
                </div>
                <article class="form-group form-group-sm {{ $errors->first('planned_finish_date', 'has-error') }}">
                    {{ Form::label('planned_finish_date', null, ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        {{ Form::date('planned_finish_date', $period->planned_finish_date->format('Y-m-d'), ['class' => 'form-control to-calendar']) }}
                        {!! $errors->first('planned_finish_date', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>
                <article class="form-group form-group-sm {{ $errors->first('forecast_finish_date', 'has-error') }}">
                    {{ Form::label('forecast_finish_date', null, ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        {{ Form::date('forecast_finish_date', $period->forecast_finish_date? \Carbon\Carbon::parse($period->forecast_finish_date)->format('Y-m-d') : '', ['class' => 'form-control to-calendar']) }}
                        {!! $errors->first('forecast_finish_date', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>



                {{--<article class="form-group {{$errors->first('planned_value', 'has-error')}}">
                    {{Form::label('planned_value', null, ['class' => 'control-label col-sm-3'])}}
                    <div class="col-sm-9">
                        {{ Form::number('planned_value', null, ['class' => 'form-control'])}}
                        {!! $errors->first('planned_value', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>--}}
            </fieldset>

            <fieldset>
                <legend>Actual Information</legend>

                {{-- <div class="form-group form-group-sm {{ $errors->first('spi_index', 'has-error') }}">
                     {{ Form::label('spi_index', 'SPI Index', ['class' => 'control-label col-sm-3']) }}
                     <div class="col-sm-9">
                         {{ Form::text('spi_index', null, ['class' => 'form-control']) }}
                         {!! $errors->first('spi_index', '<div class="help-block">:message</div>') !!}
                     </div>
                 </div>--}}

                <div class="form-group form-group-sm {{ $errors->first('spi_index', 'has-error') }}">
                    {{ Form::label('productivity_index', 'Productivity Index', ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        {{ Form::text('productivity_index', null, ['class' => 'form-control']) }}
                        {!! $errors->first('productivity_index', '<div class="help-block">:message</div>') !!}
                    </div>
                </div>



                {{--<article class="form-group form-group-sm {{ $errors->first('time_elapsed', 'has-error') }}">
                    {{ Form::label('time_elapsed', null, ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        <div class="input-group">
                            {{ Form::text('time_elapsed', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">Days</span>
                        </div>
                        {!! $errors->first('time_elapsed', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>

                <article class="form-group form-group-sm {{ $errors->first('time_remaining', 'has-error') }}">
                    {{ Form::label('time_remaining', null, ['class' => 'control-label col-sm-3']) }}
                    <div class="col-sm-9">
                        <div class="input-group">
                            {{ Form::text('time_remaining', null, ['class' => 'form-control', 'disabled']) }}
                            <span class="input-group-addon">Days</span>
                        </div>
                        {!! $errors->first('time_remaining', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>


                                <article class="form-group form-group-sm {{ $errors->first('duration_variance', 'has-error') }}">
                                    {{ Form::label('duration_variance', null, ['class' => 'control-label col-sm-3']) }}
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            {{ Form::text('duration_variance', $period->duration_variance, ['class' => 'form-control', 'disabled']) }}
                                            <span class="input-group-addon">Days</span>
                                        </div>
                                        {!! $errors->first('duration_variance', '<div class="help-block">:message</div>') !!}
                                    </div>
                                </article>
                                <article class="form-group {{$errors->first('earned_value', 'has-error')}}">
                                    {{Form::label('earned_value', null, ['class' => 'control-label col-sm-3'])}}
                                    <div class="col-sm-9">
                                        {{ Form::number('earned_value', null, ['class' => 'form-control'])}}
                                        {!! $errors->first('earned_value', '<div class="help-block">:message</div>') !!}
                                    </div>
                                </article>
                                --}}

                <article class="form-group {{$errors->first('actual_invoice_value', 'has-error')}}">
                    {{Form::label('actual_invoice_value', "Actual Invoice Value", ['class' => 'control-label col-sm-3'])}}
                    <div class="col-sm-9">
                        {{ Form::number('actual_invoice_value', null, ['class' => 'form-control'])}}
                        {!! $errors->first('actual_invoice_value', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>
            </fieldset>

            {{--<fieldset>
                <legend>Indirect At Completion</legend>

                <article class="form-group {{$errors->first('at_completion_optimistic', 'has-error')}}">
                    {{Form::label('at_completion_optimistic', 'Optimistic', ['class' => 'control-label col-sm-3'])}}
                    <div class="col-sm-9">
                        {{ Form::number('at_completion_optimistic', null, ['class' => 'form-control', 'step' => '0.01'])}}
                        {!! $errors->first('at_completion_optimistic', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>

                <article class="form-group {{$errors->first('at_completion_likely', 'has-error')}}">
                    {{Form::label('at_completion_likely', 'Most likely', ['class' => 'control-label col-sm-3'])}}
                    <div class="col-sm-9">
                        {{ Form::number('at_completion_likely', null, ['class' => 'form-control', 'step' => '0.01'])}}
                        {!! $errors->first('at_completion_likely', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>

                <article class="form-group {{$errors->first('at_completion_pessimistic', 'has-error')}}">
                    {{Form::label('at_completion_pessimistic', 'Pessimistic', ['class' => 'control-label col-sm-3'])}}
                    <div class="col-sm-9">
                        {{ Form::number('at_completion_pessimistic', null, ['class' => 'form-control', 'step' => '0.01'])}}
                        {!! $errors->first('at_completion_pessimistic', '<div class="help-block">:message</div>') !!}
                    </div>
                </article>

            </fieldset>--}}
        @endif
        <hr>

        <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
            </div>
        </div>

    </section>
</div>